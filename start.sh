#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/arkib-app"

exec composer run dev
