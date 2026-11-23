#!/usr/bin/env python3
"""Builds acf-json/group_*.json and prints the README field map from inc/fields-spec.json.
Run from anywhere: python3 bin/gen-acf.py [--table]"""
import json, sys, pathlib, time
root = pathlib.Path(__file__).resolve().parent.parent
spec = json.loads((root / "inc/fields-spec.json").read_text())
TYPE = {"text":"text","textarea":"textarea","wysiwyg":"wysiwyg","number":"number","date":"date_picker",
        "bool":"true_false","select":"select","url":"url","image":"image","gallery":"gallery",
        "post":"post_object","posts":"relationship","list":"repeater","rows":"repeater"}

def field(post, f, parent=""):
    k = "field_yaww_%s_%s%s" % (post, parent, f["key"])
    o = {"key": k, "label": f["label"], "name": f["key"], "type": TYPE[f["type"]]}
    if f.get("instructions"): o["instructions"] = f["instructions"]
    if f.get("required"): o["required"] = 1
    t = f["type"]
    if t == "select":
        names = f.get("choice_labels", {})
        o["choices"] = {c: names.get(c, c) for c in f["choices"]}; o["return_format"] = "value"
        if f.get("required"): o["default_value"] = f["choices"][0]
    if t == "date": o.update(display_format="j F Y", return_format="Y-m-d", first_day=1)
    if t == "post": o["allow_null"] = 1
    if t in ("image", "gallery"): o["return_format"] = "id"
    if t in ("post", "posts"): o["post_type"] = f["post_type"] if isinstance(f["post_type"], list) else [f["post_type"]]; o["return_format"] = "id"
    if t == "list": o["layout"] = "table"; o["button_label"] = "Add"; o["sub_fields"] = [
        {"key": k + "_value", "label": "Item", "name": "value", "type": "text"}]
    if t == "rows":
        o["layout"] = "block"; o["button_label"] = "Add row"
        o["sub_fields"] = [field(post, s, f["key"] + "_") for s in f["sub"]]
    return o

if "--table" in sys.argv:
    print("| Post type | Meta key | Type | Source in the static site |\n|---|---|---|---|")
    for p, fs in spec.items():
        if p.startswith("_"): continue
        for f in fs:
            print("| `%s` | `%s` | %s | %s |" % (p, f["key"], f["type"], f.get("src", "")))
    sys.exit()

def fields_with_tabs(post, fs):
    """ACF tab fields before every spec field that carries "tab" (the edit screen's sections)."""
    out = []
    for f in fs:
        if f.get("tab"):
            slug = "".join(ch if ch.isalnum() else "_" for ch in f["tab"].lower()).strip("_")
            out.append({"key": "field_yaww_%s_tab_%s" % (post, slug), "label": f["tab"], "name": "", "type": "tab",
                        "placement": "top", "endpoint": 0})
        out.append(field(post, f))
    return out

for p, fs in spec.items():
    if p.startswith("_"): continue
    g = {"key": "group_yaww_" + p, "title": "YAWW · " + ("Journal" if p == "post" else p.capitalize()),
         "fields": fields_with_tabs(p, fs),
         "location": [[{"param": "post_type", "operator": "==", "value": p}]],
         "menu_order": 0, "position": "normal", "style": "default",
         "label_placement": "top", "active": True, "show_in_rest": 1, "modified": int(time.time())}
    (root / "acf-json" / ("group_yaww_%s.json" % p)).write_text(json.dumps(g, indent=2) + "\n")
    print("wrote group_yaww_%s.json" % p)
