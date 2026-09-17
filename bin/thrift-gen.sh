#!/usr/bin/env bash
#
# Regenerates src/Thrift from the Jaeger IDL.
#
# The compiler version matters: 0.24 emits declare(strict_types=1) and native
# types, earlier releases emit untyped properties with @var docblocks only.
#
#   brew install thrift
#
set -euo pipefail

IDL_VERSION='v0.12.0'
THRIFT_MAJOR_MINOR='0.24'

cd "$(dirname "$0")/.."
root="$(pwd)"

if ! command -v thrift >/dev/null 2>&1; then
    echo 'error: the thrift compiler is not installed (brew install thrift)' >&2
    exit 1
fi

thrift_version="$(thrift --version | awk '{print $NF}')"
if [[ "${thrift_version}" != "${THRIFT_MAJOR_MINOR}."* ]]; then
    echo "error: thrift ${THRIFT_MAJOR_MINOR}.x is required, found ${thrift_version}" >&2
    exit 1
fi

workdir="$(mktemp -d)"
trap 'rm -rf "${workdir}"' EXIT

git clone --quiet --depth 1 --branch "${IDL_VERSION}" \
    https://github.com/jaegertracing/jaeger-idl.git "${workdir}/jaeger-idl"

cd "${workdir}/jaeger-idl"
for definition in thrift/*.thrift; do
    thrift -r --gen php "${definition}"
done

rm -rf "${root}/src/Thrift"
mv gen-php/Jaeger/Thrift "${root}/src/Thrift"

echo "src/Thrift regenerated from jaeger-idl ${IDL_VERSION} using thrift ${thrift_version}"
