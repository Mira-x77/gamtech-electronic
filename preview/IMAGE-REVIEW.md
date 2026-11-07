# Product photo review

934 price-list products arrived without photos. Most are generic or repair
parts where a random internet image would be wrong. Photos are therefore only
published after a human approves them.

## What gets queued

Only products with a **known brand and model number** (e.g. `HP 840G5`,
`ONTEN OTN-UC102`, `ASUS E406`). Generic rows such as `STICKER NOIR` or
`CABLE IMPRIMANTE 3M` are never queued: no unique photo exists for them.

A candidate is accepted into the queue only when its own title contains:

- the brand (`HP`, `Dell`, `Lenovo`, …),
- every model token (`840G5`, `E406`, `OTN-UC102`, …),
- the product type (`keyboard`, `battery`, `charger`, `screen`, …),
- and no conflicting variant (`840G3` while looking for `840G5`).

This stops the classic mistake of showing a whole laptop for an `HP 840G5`
keyboard. Even so, every candidate is a proposal — nothing is written until
someone clicks **Use this photo**.

## Build the queue

```powershell
npm run images:queue                 # all exact-model products
node preview/find-image-candidates.mjs --limit=20
node preview/find-image-candidates.mjs --cat=Chargers
node preview/find-image-candidates.mjs --id=144 --force
```

The queue is resume-safe: already queued products are skipped. Output:

- `preview/data/image-candidates.json` — verified candidates
- `preview/data/image-review/<id>/` — local thumbnails for review

Both are ignored by git.

## Approve photos

1. Open `http://localhost:3000/admin01.html`
2. Enter the desk PIN
3. Open the **Photos** tab
4. Compare the thumbnails against the product name
5. Click **Use this photo** on the correct one, or **Skip**

Approved files are saved to
`wp-content/uploads/product-images/verified/<id>-<slug>.<ext>` and recorded in
`preview/data/overrides.json` by product name. The catalog is rewritten
immediately, so the storefront updates on refresh.

## Manual photos

The **Stock** tab still supports uploading a real shop photo per product,
which always wins over a search result.

## Files

- `preview/lib/image-verify.mjs` — brand/model/type verification
- `preview/lib/find-product-image.mjs` — Bing search + candidate collection
- `preview/find-image-candidates.mjs` — queue builder
- `preview/admin-api.mjs` — `photo-candidates`, `use-photo`, `skip-photo`
