# AIMA Mine Plus — PDF hunt: repair / internal wiring

Target from photo: **AIMA MINE Plus** (white, round LED headlight, 800 W, 10" wheels)
Citrus UA SKU 620329. Specs from passport PDF: 97 kg, Li-Ion, up to 8 h charge,
wheelbase 1250 mm, max load 180 kg, up to 80 km, up to 55 km/h, 800 W motor.
Chinese domestic name searched as 爱玛 矿台 / 矿台 Plus — no service PDF found under that name either.

## Verified PDFs found (exact model)

### 1. Owner manual — exact model ✅
- **File (local):** `aima-mine-plus-user-manual-citrus.pdf` (1,026,548 bytes)
- **Source URL:** https://i.citrus.world/uploads/files/aima-mine-pluspdf.pdf
- **Language:** Ukrainian. Title: "Керівництво з експлуатації електроскутера AIMA MINE Plus"
- **Contents checked (full text extracted):** safety, parts list (20 items),
  charging, battery, motor, display, bearings, brakes, daily/professional
  inspection tables, operation, and a 4-row troubleshooting table
  (no power / motor not running / short range / won't charge).
- **Wiring / internal repair? NO.** No electrical schematic, no harness
  diagram, no pin-outs, no disassembly steps. It explicitly says:
  "не демонтуйте та не замінюйте дроти у скутері самостійно"
  (do not dismantle/replace wires yourself) and sends everything to a
  professional service centre every 3 months.
- This is an **owner's manual, not a factory service/repair manual.**

### 2. Technical passport / warranty — exact model ✅
- **File (local):** `aima-mine-plus-passport-citrus.pdf` (68,271 bytes)
- **Source URL:** https://i.citrus.world/uploads/files/aima-mine-plus-passportpdf.pdf
- **Contents checked:** spec table above + warranty card. No wiring info.

### 3. ⚠️ Wrong model — do NOT use as Mine Plus
- **File (local):** `aima-miao-27ua-user-manual.pdf` (1,987,586 bytes)
- **Source URL:** https://cdn.27.ua/product-files/f3/2c/aima-miaopdf.pdf
- Search engines index this under AIMA generally. Inside it is
  **AIMA MIAO** (a different, smaller pedal-assist model), 23 pages.
- It *does* have p.20 "Структурна діаграма" and p.21 "Схематична Діаграма"
  (schematic diagram, image-only, no extracted text) — but for MIAO,
  **not** Mine Plus. Useful only as a generic AIMA layout reference,
  not as an exact-model wiring diagram.

## Searched, NOT found

- `"AIMA Mine Plus" filetype:pdf manual wiring` (Bing/Yahoo/DuckDuckGo)
- `"AIMA MINE Plus" "wiring diagram" OR "circuit diagram" OR "service manual"` — no results
- `爱玛 矿台 电路图 pdf` / `爱玛矿台plus 电路图 线束` / `爱玛 矿台 维修手册` — no results
- ManualMachine AIMA index (https://manualmachine.com/aima/) lists only:
  Journey, M3 Li, N310, Power Eagle — **no Mine Plus**
- ManualsLib AIMA brand page — no Mine Plus service manual
- AIMA official global support (https://global.aimatech.com/support) —
  dealer/FAQ only, **no manual downloads**
- Baidu `爱玛矿台plus电路图维修手册 filetype:pdf` — captcha-blocked

## Closest internal-wiring lead (not a PDF)

Forum thread dedicated to this exact model asking for the display schematic:
- https://eworld.com.ua/elektroskuter-aima-mine-plus/shema-displeya-elektricheskaya-princzipialnaya/
- Topic: "Схема дисплея электрическая принципиальная" (Display electrical schematic)
- A user is *asking* for the Rx/Tx commutation part between connector and
  processor; a reply asks them to post the schematic. No diagram/PDF is
  attached in the public thread (attachments require registration).
- Forum index: https://eworld.com.ua/elektroskuter-aima-mine-plus/

## Bottom line

**No public PDF of a factory repair/service manual or full internal
wiring/harness diagram for the exact AIMA Mine Plus was found.**
The only exact-model PDFs in circulation are the Citrus owner manual +
passport above. AIMA appears to distribute service documentation only
through dealers / Citrus service centres, not as public downloads.

If you need to work on the harness, the practical next step is either:
1. Register on eworld.com.ua and ask in the Mine Plus forum (above) — the
   display-schematic thread is already open, or
2. Ask a Citrus / AIMA dealer service centre for the controller/display
   pin-out for your VIN/serial — the owner manual says all wiring work
   is service-centre only.
