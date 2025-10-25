---
layout: "../../layouts/PostLayout.astro"
title: "seam-carving"
description: "content‑aware image resizing with live seam previews."
pubDate: "2025-10-06"
icon: "/projects/seam-carving/icon.svg"
technologies: ["python", "streamlit", "opencv", "numpy", "docker"]
status: "completed"
live: "https://seamcarving.tiscacatalin.com/"
source: "https://github.com/in-th3-l00p/seam-carving"
priority: 3
---

## content‑aware image retargeting
content‑aware retargeting via dynamic programming: upload, set size, preview seams, download.

### general info
- **tech**: Python 3.11, Streamlit, OpenCV, NumPy, Docker
- **algorithm**: Sobel (grayscale) energy → DP cumulative costs (vertical & horizontal) → seam backtracking → seam removal (vectorized)
- **UI**: image uploader, width/height sliders, centered non‑stretch rendering, 3‑tile grids for original and resized seam previews, persistent download
- **shape**: small library in `src/seam_carving/` + modular UI in `src/ui/` + Streamlit entry in `src/main.py`

### why it matters
uniform scaling squashes important content. seam carving removes low‑energy paths to better preserve salient regions. this project pairs a readable implementation with an interactive UI to make the idea tangible.

### architecture
#### algorithm
- energy: grayscale Sobel gradients, summed magnitude
- DP (vectorized): for each row/column, pick predecessor with minimal cumulative cost; store step deltas in {-1, 0, +1}
- backtracking: trace minimal seam from the last row/column using stored step deltas
- seam removal: boolean‑mask + reshape (vectorized) for vertical and horizontal seams
- scheduling: interleave vertical/horizontal removals (Bresenham‑like) to reduce distortion

### data flow
- upload → decode (BGR→RGB) → show next seams on original
- sliders select target size (must be ≤ original)
- carve → store result in session → show result + next seams → download PNG (persists on rerun)

### notes
- currently supports shrinking only (no expansion)
- performance scales with pixels × seams; consider pre‑resizing very large images
- Sobel energy is fast and effective; swapping in Scharr/forward energy is a natural extension
- next seams are previews; they change after each removal by design
