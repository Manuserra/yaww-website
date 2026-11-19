#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Validate every retreats/<slug>/data.json against _schema.json and check
that every image it references exists on disk.

Run from anywhere:  python3 retreats/_validate.py
Exit code 0 = clean, 1 = problems found. No third-party dependencies: the
subset of JSON Schema the schema actually uses is implemented below, so this
runs on a bare python3.
"""
import json, os, re, sys, datetime

HERE = os.path.dirname(os.path.abspath(__file__))
REPO = os.path.dirname(HERE)
SCHEMA = json.load(open(os.path.join(HERE, "_schema.json"), encoding="utf-8"))
INDEX = json.load(open(os.path.join(HERE, "_index.json"), encoding="utf-8"))

TYPES = {"string": str, "integer": int, "number": (int, float), "boolean": bool,
         "object": dict, "array": list, "null": type(None)}

errors, warnings = [], []


def err(where, msg): errors.append(f"{where}: {msg}")
def warn(where, msg): warnings.append(f"{where}: {msg}")


def resolve(node):
    """Follow a local $ref inside SCHEMA."""
    while isinstance(node, dict) and "$ref" in node:
        path = node["$ref"].lstrip("#/").split("/")
        n = SCHEMA
        for p in path:
            n = n[p]
        node = n
    return node


def type_ok(value, spec):
    t = spec.get("type")
    if t is None:
        return True
    names = t if isinstance(t, list) else [t]
    for n in names:
        py = TYPES.get(n)
        if py is None:
            continue
        if n == "integer" and isinstance(value, bool):
            continue
        if n == "number" and isinstance(value, bool):
            continue
        if isinstance(value, py):
            return True
    return False


def check(value, spec, where):
    spec = resolve(spec)
    if "allOf" in spec:
        for sub in spec["allOf"]:
            check(value, sub, where)
        return
    if "enum" in spec:
        if value not in spec["enum"]:
            err(where, f"{value!r} is not one of {spec['enum']}")
        return
    if not type_ok(value, spec):
        err(where, f"expected type {spec.get('type')}, got {type(value).__name__} ({str(value)[:60]!r})")
        return
    if isinstance(value, dict):
        for r in spec.get("required", []):
            if r not in value:
                err(where, f"missing required field '{r}'")
        props = spec.get("properties", {})
        for k, v in value.items():
            if k in props:
                check(v, props[k], f"{where}.{k}")
            elif k.startswith("_"):
                pass                      # provenance fields are free-form
            elif props and not spec.get("additionalProperties", True):
                err(where, f"unexpected field '{k}'")
    elif isinstance(value, list) and "items" in spec:
        for i, v in enumerate(value):
            check(v, spec["items"], f"{where}[{i}]")
    if isinstance(value, str) and "maxLength" in spec and len(value) > spec["maxLength"]:
        err(where, f"longer than {spec['maxLength']} characters")


def walk_images(node, path, out):
    """Collect every { file, original_url } object in a document."""
    if isinstance(node, dict):
        if "original_url" in node and "file" in node:
            out.append((path, node))
        for k, v in node.items():
            walk_images(v, f"{path}.{k}", out)
    elif isinstance(node, list):
        for i, v in enumerate(node):
            walk_images(v, f"{path}[{i}]", out)


def main():
    idx_slugs = [r["slug"] for r in INDEX["retreats"]] + [r["slug"] for r in INDEX.get("waitlist", [])]
    dirs = sorted(d for d in os.listdir(HERE)
                  if os.path.isdir(os.path.join(HERE, d)) and not d.startswith("_"))
    if set(idx_slugs) != set(dirs):
        err("_index.json", f"index and folders disagree: only in index {sorted(set(idx_slugs) - set(dirs))}, "
                           f"only on disk {sorted(set(dirs) - set(idx_slugs))}")

    seen_files, total_imgs, ok_imgs = {}, 0, 0

    for slug in dirs:
        p = os.path.join(HERE, slug, "data.json")
        where = f"{slug}/data.json"
        if not os.path.exists(p):
            err(where, "missing"); continue
        try:
            d = json.load(open(p, encoding="utf-8"))
        except Exception as e:
            err(where, f"not valid JSON: {e}"); continue

        check(d, SCHEMA, slug)

        # --- cross-checks the schema cannot express ---------------------
        if d.get("slug") != slug:
            err(where, f"slug field {d.get('slug')!r} does not match the folder name")
        # A waitlist program whose page publishes no dates has none to check.
        if not (d.get("status") == "waitlist" and d.get("date_start") is None):
            try:
                a = datetime.date.fromisoformat(d["date_start"])
                b = datetime.date.fromisoformat(d["date_end"])
                if b <= a:
                    err(where, "date_end is not after date_start")
                if d.get("nights") is not None and d["nights"] != (b - a).days:
                    err(where, f"nights={d['nights']} but the dates span {(b - a).days} nights")
                if d.get("days") is not None and d["days"] != (b - a).days + 1:
                    err(where, f"days={d['days']} but the dates span {(b - a).days + 1} days")
            except (KeyError, ValueError, TypeError) as e:
                err(where, f"dates not ISO 8601: {e}")

        if len(d.get("seo_title") or "") > 60:
            err(where, f"seo_title is {len(d['seo_title'])} characters (max 60)")
        if len(d.get("seo_description") or "") > 155:
            err(where, f"seo_description is {len(d['seo_description'])} characters (max 155)")

        if (d.get("tier") is None) != (d.get("tier_label") is None):
            err(where, "tier and tier_label disagree")
        if d.get("teacher_unassigned") != (len(d.get("teachers") or []) == 0):
            err(where, "teacher_unassigned does not match the teachers list")

        for k in d.get("_missing", []):
            m = re.match(r'^(\w+)\[([^\]]*)\]\.(\w+)$', k)
            if m:                                 # teachers[Name].bio / rooms[].price_usd
                coll, who, field = m.groups()
                items = d.get(coll) or []
                if who:
                    items = [i for i in items if isinstance(i, dict) and i.get("name") == who]
                    if not items:
                        warn(where, f"_missing names '{who}' but no such entry exists")
                        continue
                if items and all(isinstance(i, dict) and i.get(field) for i in items):
                    warn(where, f"_missing lists '{k}' but every entry has a value")
                continue
            top = k.split(".")[0]
            if top in d and d[top] not in (None, [], {}, ""):
                warn(where, f"_missing lists '{k}' but the field has a value")

        # --- images -----------------------------------------------------
        imgs = []
        walk_images(d, slug, imgs)
        for path, im in imgs:
            total_imgs += 1
            if im.get("_unresolved") or not im.get("file"):
                err(path, "image has no file (still unresolved)"); continue
            fp = os.path.join(REPO, im["file"])
            if not os.path.exists(fp):
                err(path, f"file not found: {im['file']}")
            elif os.path.getsize(fp) < 2000:
                err(path, f"file suspiciously small: {im['file']}")
            else:
                ok_imgs += 1
            if not im.get("alt"):
                err(path, f"no alt text for {im['file']}")
            name = os.path.basename(im["file"])
            if not re.match(r'^(yoga-retreat-[a-z0-9-]+|yoga-teacher-[a-z0-9-]+)(-\d{2})?\.(jpg|png|webp)$', name):
                err(path, f"file name breaks the SEO naming rule: {name}")
            if re.search(r'img[-_]?\d{3,}|^\d+\.', name, re.I):
                err(path, f"file name looks auto-generated: {name}")
            prev = seen_files.get(im["file"])
            if prev and prev != im["original_url"]:
                err(path, f"{im['file']} is used for two different source URLs")
            seen_files[im["file"]] = im["original_url"]
            if im["file"].startswith("retreats/_shared/") and im.get("shared") is False:
                warn(path, "lives in _shared but shared is false")
            if not im["file"].startswith("retreats/_shared/") and im.get("shared"):
                warn(path, "marked shared but does not live in _shared")

    # --- orphan files on disk ------------------------------------------
    on_disk = set()
    for root, _dirs, files in os.walk(HERE):
        for f in files:
            if f.lower().endswith((".jpg", ".jpeg", ".png", ".webp")):
                on_disk.add(os.path.relpath(os.path.join(root, f), REPO))
    orphans = sorted(on_disk - set(seen_files))
    for o in orphans:
        warn("orphan", f"{o} is on disk but no data.json references it")

    print(f"retreats checked : {len(dirs)}")
    print(f"image references : {total_imgs}  ({ok_imgs} resolved to a real file)")
    print(f"distinct files   : {len(seen_files)}")
    print(f"errors           : {len(errors)}")
    print(f"warnings         : {len(warnings)}")
    for e in errors[:200]:
        print("  ERROR  ", e)
    if len(errors) > 200:
        print(f"  ... and {len(errors) - 200} more errors")
    for w in warnings[:60]:
        print("  warn   ", w)
    if len(warnings) > 60:
        print(f"  ... and {len(warnings) - 60} more warnings")
    return 1 if errors else 0


if __name__ == "__main__":
    sys.exit(main())
