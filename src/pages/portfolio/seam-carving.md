---
layout: "../../layouts/PostLayout.astro"
title: "seam-carving"
description: "content‑aware image resizing in Python; Streamlit app with live seam previews."
pubDate: "2025-10-06"
icon: "/projects/seam-carving/icon.svg"
technologies: ["python", "streamlit", "opencv", "numpy", "docker"]
status: "completed"
live: "https://seamcarving.tiscacatalin.com/"
source: "https://github.com/in-th3-l00p/seam-carving"
priority: 1
---

## content‑aware image retargeting
content‑aware image retargeting using dynamic programming. upload an image, choose target dimensions, visualize the next vertical/horizontal seams, and download the carved result. built for clarity and interactivity.

### general info
- **tech**: Python 3.11, Streamlit, OpenCV, NumPy, Docker
- **algorithm**: Sobel (grayscale) energy → DP cumulative costs (vertical & horizontal) → seam backtracking → seam removal (vectorized)
- **UI**: image uploader, width/height sliders, centered non‑stretch rendering, 3‑tile grids for original and resized seam previews, persistent download
- **shape**: small library in `src/seam_carving/` + modular UI in `src/ui/` + Streamlit entry in `src/main.py`
- **goal**: clarity—see how seams are chosen and how content‑aware shrinking affects structure

### why it matters
uniform scaling squashes important content. seam carving removes low‑energy paths to better preserve salient regions. this project pairs a readable implementation with an interactive UI to make the idea tangible.

### architecture
#### algorithm
- energy: grayscale Sobel gradients, summed magnitude
- DP (vectorized): for each row/column, pick predecessor with minimal cumulative cost; store step deltas in {-1, 0, +1}
- backtracking: trace minimal seam from the last row/column using stored step deltas
- seam removal: boolean‑mask + reshape (vectorized) for vertical and horizontal seams
- scheduling: interleave vertical/horizontal removals (Bresenham‑like) to reduce distortion

#### ui
- `src/ui/components.py`: caption, original grid (original + next seams), resized grid (result + next seams + download)
- `src/ui/state.py`: session state init/reset, storing carved result and PNG bytes (survives reruns)
- `src/ui/__init__.py`: `run_app()` wires Streamlit page config, state, upload, sliders, carving, and grids
- `src/main.py`: thin entry—imports and calls `run_app()`

### data flow
- upload → decode (BGR→RGB) → show next seams on original
- sliders select target size (must be ≤ original)
- carve → store result in session → show result + next seams → download PNG (persists on rerun)

### execution flow
- original
  - compute energy and DP tables
  - preview next vertical/horizontal seams
- resize
  - interleave vertical/horizontal seam removal until target
  - recompute tables each step to maintain correctness
- result
  - preview next seams on the resized image
  - download PNG

### build and run
requirements: Python 3.11 with virtualenv (or Docker)

#### local
```bash
# from project root
source ./bin/activate
pip install -r ./requirements.txt
streamlit run src/main.py
```

#### docker
```bash
docker build -t seam-carving-app .
docker run --rm -p 8501:8501 seam-carving-app
```

### notes
- currently supports shrinking only (no expansion)
- performance scales with pixels × seams; consider pre‑resizing very large images
- Sobel energy is fast and effective; swapping in Scharr/forward energy is a natural extension
- next seams are previews; they change after each removal by design

### credits
by Tisca Catalin (intheloop) — [tiscacatalin.com](https://tiscacatalin.com)

