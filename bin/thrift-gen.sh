#!/bin/sh
set -e

cd "$(dirname "$0")/.."

git clone https://github.com/jaegertracing/jaeger-idl
pushd jaeger-idl

rm -rf ../src/Thrift

FILES=thrift/*.thrift
for f in ${FILES}; do
  thrift -r --gen php:psr4 ${f}
done

rm -rf ../src/Jaeger/Thrift/
mv ../jaeger-idl/gen-php/Jaeger/Thrift ../src/Thrift

popd
rm -rf jaeger-idl
