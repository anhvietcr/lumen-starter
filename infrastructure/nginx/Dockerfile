FROM realpage/nginx:1.11
MAINTAINER Foundation DevOps foundation-devops@realpage.com
LABEL com.realpage.nginx.version="1.11"

# Copy the application public files to the container
ADD public /var/www/html/public
ADD resources/docs /var/www/html/resources/docs
ADD infrastructure/nginx/default.conf /etc/nginx/conf.d/default.conf
WORKDIR /var/www/html

# Give access of public dir to nginx
RUN chown -R nginx:nginx public/