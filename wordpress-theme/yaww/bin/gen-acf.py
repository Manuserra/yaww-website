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
    t = f["type"]
    if t == "select": o["choices"] = {c: c for c in f["choices"]}; o["return_format"] = "value"
    if t == "date": o.update(display_format="Y-m-d", return_format="Y-m-d")
    if t == "post": o["allow_null"] = 1
    if t in ("image", "gallery"): o["return_format"] = "id"
    if t in ("post", "posts"): o["post_type"] = f["post_type"] if isinstance(f["post_type"], list) else [f["post_type"]]; o["return_format"] = "id"
    if t == "list": o["layout"] = "table"; o["button_label"] = "Add"; o["sub_fields"] = [
        {"key": k + "_value", "label": "Value", "name": "value", "type": "text"}]
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

for p, fs in spec.items():
    if p.startswith("_"): continue
    g = {"key": "group_yaww_" + p, "title": "YAWW · " + ("Journal" if p == "post" else p.capitalize()),
         "fields": [field(p, f) for f in fs],
         "location": [[{"param": "post_type", "operator": "==", "value": p}]],
         "menu_order": 0, "position": "normal", "style": "default",
         "label_placement": "top", "active": True, "show_in_rest": 1, "modified": int(time.time())}
    (root / "acf-json" / ("group_yaww_%s.json" % p)).write_text(json.dumps(g, indent=2) + "\n")
    print("wrote group_yaww_%s.json" % p)
