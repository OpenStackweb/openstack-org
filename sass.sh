#!/usr/bin/env bash

# add new modules here in order to preproccess scss
modules=( themes/openstack errors_pages/ui/404 marketplace/code/ui/frontend marketplace/code/ui/admin events \
news/code/ui/frontend survey_builder user-stories coa dupe_members papers \
summit elections jobs software speaker_bureau registration summit-trackchair-app)

for module in "${modules[@]}"
do
    ./node_modules/node-sass/bin/node-sass $module/scss -r -o $module/css
done

# copy static assets

mkdir -p themes/openstack/css/blueprint/plugins/buttons/icons
cp -R themes/openstack/scss/blueprint/plugins/buttons/icons themes/openstack/css/blueprint/plugins/buttons

mkdir -p themes/openstack/css/blueprint/plugins/link-icons/icons
cp -R themes/openstack/scss/blueprint/plugins/link-icons/icons themes/openstack/css/blueprint/plugins/link-icons

mkdir -p themes/openstack/css/anniversary/4/fonts;
cp -R themes/openstack/scss/anniversary/4/fonts themes/openstack/css/anniversary/4;

cp themes/openstack/scss/chosen-sprite.png themes/openstack/css