"""Rewrite product photos browsers cannot decode (JPEG 2000, TIFF, BMP, HEIC, etc.)."""
from pathlib import Path

from typing import Optional, Tuple

from PIL import Image, UnidentifiedImageError

ROOTS = [
    Path(__file__).resolve().parent.parent / "wp-content" / "uploads" / "product-images",
    Path(__file__).resolve().parent.parent / "product-images",
]

NATIVE = {"JPEG", "PNG", "WEBP", "GIF", "AVIF"}


def flatten(im: Image.Image) -> Image.Image:
    if im.mode in ("RGB", "L"):
        return im.convert("RGB")
    rgba = im.convert("RGBA")
    bg = Image.new("RGB", rgba.size, (255, 255, 255))
    bg.paste(rgba, mask=rgba.split()[-1])
    return bg


def save_as(im: Image.Image, path: Path) -> None:
    ext = path.suffix.lower()
    tmp = path.with_name(path.name + ".tmp")
    if ext in {".jpg", ".jpeg", ".jfif"}:
        flatten(im).save(tmp, "JPEG", quality=92, optimize=True)
    elif ext == ".webp":
        im.convert("RGBA").save(tmp, "WEBP", quality=90)
    else:
        im.convert("RGBA").save(tmp, "PNG", optimize=True)
    tmp.replace(path)


def sniff_needs_rewrite(path: Path) -> Optional[Tuple[Image.Image, str]]:
    try:
        with Image.open(path) as opened:
            opened.load()
            fmt = (opened.format or "").upper()
            if fmt in NATIVE:
                return None
            return opened.copy(), fmt
    except (UnidentifiedImageError, OSError):
        return None


def main() -> None:
    converted = 0
    for root in ROOTS:
        if not root.is_dir():
            continue
        for path in root.rglob("*"):
            if not path.is_file():
                continue
            if path.suffix.lower() not in {
                ".jpg",
                ".jpeg",
                ".jfif",
                ".png",
                ".webp",
                ".gif",
                ".bmp",
                ".tif",
                ".tiff",
                ".jp2",
                ".j2k",
                ".heic",
                ".heif",
                ".avif",
                ".ico",
            }:
                continue
            loaded = sniff_needs_rewrite(path)
            if loaded is None:
                continue
            im, fmt = loaded
            save_as(im, path)
            converted += 1
            print(f"converted {path} ({fmt} -> browser-safe)")
    print(f"normalized {converted} images")


if __name__ == "__main__":
    main()
