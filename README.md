docker build -t pdf-fusion:latest .

docker run -p 8080:80 pdf-fusion:latest

docker tag pdf-fusion  harbor.swhome.lan:4443/library/pdf-fusion:latest

docker login harbor.swhome.lan:4443

docker push harbor.swhome.lan:4443/library/pdf-fusion:latest
