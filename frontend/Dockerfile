FROM nginx:alpine

ENV API_URL=http://api:8000

COPY . /usr/share/nginx/html
RUN mkdir -p /etc/nginx/templates && mv /usr/share/nginx/html/nginx.conf.template /etc/nginx/templates/default.conf.template