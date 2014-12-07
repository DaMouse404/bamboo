#Paramaterized build
#SHA=
#PR=

USERNAME=$1
PASSWORD=$2
ACCESS_TOKEN=$3
CLONE_PASSWORD=$4

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
    curl --silent --insecure --data "$POST" https://api.github.com/repos/BBC/bamboo/statuses/$SHA?access_token=$3
    set +e
}

finish() {
    git checkout develop
    git branch -D pr/$PR
    exit $1
}

update_github 1 "Build in progress..." $ACCESS_TOKEN

# Checkout repo
curl -u $USERNAME:$PASSWORD https://api.github.com/repos/BBC/bamboo/contents/scripts/configure-repo.sh | ./jq '.content' --raw-output | base64 -di > configure-repo.sh
chmod +x configure-repo.sh

./configure-repo.sh $ACCESS_TOKEN $CLONE_PASSWORD

cd bamboo

# Add PR remotes
git fetch origin

# Ensure that we dont have any branches knocking around from an aborted Hudson job
# Ignore any failures from this command
git branch -D pr/$PR || true
git checkout pr/$PR

# Install dependencies
make

if [ ! $? -eq 0 ]; then
    echo "failure"
    update_github 2 "Check build failed (dependencies)" $ACCESS_TOKEN
    finish 1
fi

# Run tests
make test

if [ ! $? -eq 0 ]; then
    echo "failure"
    update_github 2 "Check build failed (makefile)" $ACCESS_TOKEN
    finish 1
fi

update_github 0 "Everything looks good" $ACCESS_TOKEN
finish 0
