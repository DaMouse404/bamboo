#Paramaterized build
#SHA=
#PR=

#$1 - username
#$2 - password
#$3 - access_token & clone username
#$4 - clone password

update_github() {
    echo 'update github'
    set -e
    if [ $1 -eq 0 ]; then
        STATUS="success"
    elif [ $1 -eq 1 ]; then
        STATUS="pending"
    else
        STATUS="failure"
    fi
    POST="{\"state\":\"${STATUS}\",\"target_url\":\"${BUILD_URL}console\",\"description\":\"$2\"}"
    curl --silent --insecure --data "$POST" https://api.github.com/repos/iplayer/bamboo/statuses/$SHA?access_token=$3
    set +e
}

finish() {
    git checkout develop
    git branch -D pr/$PR
    exit $1
}

update_github 1 "Build in progress..." $3

# Checkout repo
curl -u $1:$2 https://api.github.com/repos/craigtaub/bamboo/contents/scripts/configure-repo.sh?ref=19050 | ./jq '.content' --raw-output | base64 -di > configure-repo.sh
chmod +x configure-repo.sh

./configure-repo.sh $3 $4

cd bamboo

# Add PR remotes
git fetch origin

# Ensure that we dont have any branches knocking around from an aborted Hudson job
# Ignore any failures from this command
git branch -D pr/$PR || true
git checkout pr/$PR

# Install composer dependencies
./scripts/setup_composer.sh

if [ ! $? -eq 0 ]; then
    echo "failure"
    update_github 2 "Check build failed (composer)" $3
    finish 1
fi

# Run makefile
make test

if [ ! $? -eq 0 ]; then
    echo "failure"
    update_github 2 "Check build failed (makefile)" $3
    finish 1
fi

update_github 0 "Everything looks good" $3
finish 0
