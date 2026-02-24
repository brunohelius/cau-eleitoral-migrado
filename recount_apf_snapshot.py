#!/usr/bin/env python3
"""Wrapper to run the APF snapshot recount script from the repository root."""

from pathlib import Path
import runpy


if __name__ == "__main__":
    script = Path(__file__).resolve().parent / "scripts" / "recount_apf_snapshot.py"
    runpy.run_path(str(script), run_name="__main__")
