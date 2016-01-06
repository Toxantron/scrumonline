#
# Copyright (c) 2012-2015 Codenvy, S.A.
# All rights reserved. This program and the accompanying materials
# are made available under the terms of the Eclipse Public License v1.0
# which accompanies this distribution, and is available at
# http://www.eclipse.org/legal/epl-v10.html
#
# Contributors:
#   Codenvy, S.A. - initial API and implementation
#

FROM codenvy/php56_apache2

# Load source tree to web root
ADD $src$ /var/www/
RUN sudo rm -r /var/www/html && \
    sudo mv /var/www/src /var/www/html && \
    sudo chown -R $APACHE_RUN_USER:$APACHE_RUN_GROUP /var/www/html
    
# Run composer and doctrine
RUN cd /var/www && \
    sudo php bin/composer install -q && \
    sudo php vendor/bin/doctrine orm:generate-proxies && \
    sudo chown -R $APACHE_RUN_USER:$APACHE_RUN_GROUP vendor
    
# Mount src to copy changes later
RUN cd /home/user && mkdir scrum_online
ENV CODENVY_APP_BIND_DIR /home/user/scrum_online
VOLUME ["/home/user/scrum_online"]