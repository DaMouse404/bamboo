REPOSITORY=bamboo
BRANCH=develop
USERNAME=$1
PASSWORD=$2
OWNER=iplayer

while getopts ":b:u:p:o": opt; do
    case $opt in
        b)
            BRANCH=$OPTARG
            ;;
        u)
            USERNAME=$OPTARG
            ;;
        p)
            PASSWORD=$OPTARG
            ;;
        o)
            OWNER=$OPTARG
            ;;
        \?)
            echo "usage : configure-repo [-b branch] [-u username] [-p password] [-o owner]"
            echo
            echo "    b : the branch to use"
            echo "    u : the git username to connect to git with"
            echo "    p : the git password to use"
            echo "    o : the user account which owns the repository"
            exit 0
            ;;
    esac
done

echo "Using REPOSITORY '$REPOSITORY'"
echo "Using BRANCH '$BRANCH'"
echo "Using USERNAME '$USERNAME'"
echo "Using PASSWORD '$PASSWORD'"
echo "Using OWNER '$OWNER'"

mkdir -p $REPOSITORY

cd $REPOSITORY

if [ ! -d ".git" ]; then
    GIT_SSL_NO_VERIFY=true git clone https://$USERNAME:$PASSWORD@github.com/$OWNER/$REPOSITORY.git .
    git config --add remote.origin.fetch "+refs/pull/*/head:refs/remotes/origin/pr/*"
    git config http.sslVerify false
fi

git checkout $BRANCH

git pull

cd ../
exit 0

