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
    if f.get("width"): o["wrapper"] = {"width": str(f["width"]), "class": "", "id": ""}
    t = f["type"]
    if t == "select":
        names = f.get("choice_labels", {})
        o["choices"] = {c: names.get(c, c) for c in f["choices"]}; o["return_format"] = "value"
        if f.get("required"): o["default_value"] = f["choices"][0]
        # "ui": radio / button_group show every option at once (same stored value as a select).
        if f.get("ui") in ("radio", "button_group"):
            o["type"] = f["ui"]; o["layout"] = "vertical" if f["ui"] == "radio" else "horizontal"
            o["allow_null"] = 0 if f.get("required") else 1; o["other_choice"] = 0
        else:
            o["ui"] = 1; o["allow_null"] = 0 if f.get("required") else 1
    # Dates: stored Ymd by ACF, returned Y-m-d (yaww_date_ymd / yaww_date_range expect it), shown "Mar 4, 2027".
    if t == "date": o.update(display_format="M j, Y", return_format="Y-m-d", first_day=1)
    if t == "post": o.update(allow_null=0 if f.get("required") else 1, ui=1, multiple=0)
    if t == "posts": o.update(filters=["search"], elements=["featured_image"], min="", max="")
    if t == "image": o.update(return_format="id", preview_size="medium", library="all", mime_types="jpg,jpeg,png,webp,gif")
    if t == "gallery": o.update(return_format="id", preview_size="thumbnail", library="all", insert="append", mime_types="jpg,jpeg,png,webp,gif")
    if t == "bool": o.update(ui=1, ui_on_text="Yes", ui_off_text="No", default_value=0)
    if t == "wysiwyg": o.update(tabs="all", toolbar="basic", media_upload=0, delay=0)
    if t == "url": o["placeholder"] = "https://"
    if t in ("post", "posts"): o["post_type"] = f["post_type"] if isinstance(f["post_type"], list) else [f["post_type"]]; o["return_format"] = "id"
    if t == "list": o["layout"] = "table"; o["button_label"] = "Add a line"; o["sub_fields"] = [
        {"key": k + "_value", "label": "Item", "name": "value", "type": "text"}]
    if t == "rows":
        o["layout"] = "block"; o["button_label"] = "Add a row"
        o["sub_fields"] = [field(post, s, f["key"] + "_") for s in f["sub"]]
        # Folded rows show this sub field as their title (admin.js folds long repeaters on load).
        if f.get("collapsed"): o["collapsed"] = "%s_%s" % (k, f["collapsed"])
    return o

if "--table" in sys.argv:
    print("| Post type | Meta key | Type | Source in the static site |\n|---|---|---|---|")
    for p, fs in spec.items():
        if p.startswith("_"): continue
        for f in fs:
            print("| `%s` | `%s` | %s | %s |" % (p, f["key"], f["type"], f.get("src", "")))
    sys.exit()

# Edit screen per post type. Retreat, location, teacher and facilitator use the classic screen
# (inc/admin.php turns the block editor off): fields right under the title, no box around them.
# Journal posts keep the block editor, where meta boxes live in the panel under the text.
HIDE = ["the_content", "excerpt", "discussion", "comments", "revisions", "slug", "author", "format",
        "categories", "tags", "send-trackbacks"]
SCREEN = {
    "retreat":     {"hide": HIDE + ["featured_image", "page_attributes"]},   # hero photo = featured image (synced)
    "location":    {"hide": HIDE + ["featured_image"]},
    "teacher":     {"hide": HIDE},          # Portrait photo (featured image) stays in the sidebar
    "facilitator": {"hide": HIDE},
    "post":        {"hide": [], "position": "normal", "style": "default"},
}

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
    sc = SCREEN.get(p, {"hide": HIDE})
    g = {"key": "group_yaww_" + p, "title": "Journal post details" if p == "post" else "YAWW · " + p.capitalize(),
         "fields": fields_with_tabs(p, fs),
         "location": [[{"param": "post_type", "operator": "==", "value": p}]],
         "menu_order": 0, "position": sc.get("position", "acf_after_title"), "style": sc.get("style", "seamless"),
         "label_placement": "top", "instruction_placement": "label", "hide_on_screen": sc["hide"],
         "active": True, "show_in_rest": 1, "modified": int(time.time())}
    (root / "acf-json" / ("group_yaww_%s.json" % p)).write_text(json.dumps(g, indent=2) + "\n")
    print("wrote group_yaww_%s.json" % p)
