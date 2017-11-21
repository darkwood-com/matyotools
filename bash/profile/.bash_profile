# php
export PATH="$(brew --prefix homebrew/php/php71)/bin:$PATH"

# bash color
parse_git_branch() {
    git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/ (\1)/'
}

export PS1="[\t]\[\e[0;33m\][\[\e[0;36m\]\u@\h \[\e[0;32m\]\w\[\e[0;33m\]]\[\e[0;31m\]\$(parse_git_branch)\[\e[0m\]\$ "
export CLICOLOR=1
export LSCOLORS=ExFxBxDxCxegedabagacad
alias ls='ls -GFh'

# SSH Bash completion
complete -o default -o nospace -W "$(/usr/bin/env ruby -ne 'puts $_.split(/[,\s]+/)[1..-1].reject{|host| host.match(/\*|\?/)} if $_.match(/^\s*Host\s+/);' < $HOME/.ssh/config)" scp sftp ssh

# Google Traduction
# $ wget git.io/trans
# $ mv trans /usr/local/bin/trans
# $ chmod a+x /usr/local/bin/trans
# $ brew install gawk
# $ trans :fr "hello"

# git
GIT_AUTHOR_NAME="Mathieu Ledru"
GIT_COMMITTER_NAME="$GIT_AUTHOR_NAME"
GIT_AUTHOR_EMAIL="matyo91@gmail.com"
GIT_COMMITTER_EMAIL="$GIT_AUTHOR_EMAIL"
GIT_MERGE_AUTOEDIT=no
# git config --global user.name "$GIT_AUTHOR_NAME"
# git config --global user.email "$GIT_AUTHOR_EMAIL"

alias gs='git status'
alias gd='git diff'
alias gc='git commit -a -m'
alias gup='git up'
alias gprune="git branch -vv | grep 'origin/.*: gone]' | awk '{print $1}' | xargs git branch -d"

# git pull to branch
function gitPullToBranch() {
	if [ -z "$1" ]; then
		git pull
	else
		current_branch="$(git branch | grep '* ' | tr -d '* ')"
		for var in "$@"
		do
			git checkout $var
			git pull
		done
		git checkout $current_branch
	fi
}
alias gpull='gitPullToBranch'

# git push to branch
function gitPushToBranch() {
	if [ -z "$1" ]; then
		git push
	else
		current_branch="$(git branch | grep '* ' | tr -d '* ')"
		for var in "$@"
		do
			git checkout $var
			git push
		done
		git checkout $current_branch
	fi
}
alias gpush='gitPushToBranch'

# git merge to branch
function gitMergeToBranch() {
	current_branch="$(git branch | grep '* ' | tr -d '* ')"
	for var in "$@"
	do
		git checkout $var
		git merge $current_branch
	done
	git checkout $current_branch
}
alias gm='gitMergeToBranch'

# git execute to branch
function gitExecuteToBranch() {
	current_branch="$(git branch | grep '* ' | tr -d '* ')"
	git checkout $1
	eval $2
	git checkout $current_branch
}
alias gx='gitExecuteToBranch'

# git emoji
function gitCommitEmotion() {
    # http://emoji-cheat-sheet.com/
    emojisList=(
        "ambulance:"$'\xF0\x9F\x9A\x91'
        "beers:"$'\xF0\x9F\x8D\xBB'
        "book:"$'\xF0\x9F\x93\x96'
        "bookmark:"$'\xF0\x9F\x94\x96'
        "books:"$'\xF0\x9F\x93\x9A'
        "bug:"$'\xF0\x9F\x90\x9B'
        "confetti_ball:"$'\xF0\x9F\x8E\x8A'
        "construction:"$'\xF0\x9F\x91\xB7'
        "crown:"$'\xF0\x9F\x91\x91'
        "blush:"$'\xF0\x9F\x98\x8A'
        "globe_with_meridians:"$'\xF0\x9F\x8C\x90'
        "hammer:"$'\xF0\x9F\x94\xA8'
        "lipstick:"$'\xF0\x9F\x92\x84'
        "package:"$'\xF0\x9F\x93\xA6'
        "pencil:"$'\xF0\x9F\x93\x9D'
        "poop:"$'\xF0\x9F\x92\xA9'
        "racehorse:"$'\xF0\x9F\x8F\x87'
        "rotating_light:"$'\xF0\x9F\x9A\xA8'
        "simple_smile:"$'\xF0\x9F\x99\x82'
        "smile:"$'\xF0\x9F\x98\x80'
        "smirk:"$'\xF0\x9F\x98\x8F'
        "sparkles:"$'\xE2\x9C\xA8'
        "sunglasses:"$'\xF0\x9F\x98\x8E'
        "tada:"$'\xF0\x9F\x8E\x89'
        "wink:"$'\xF0\x9F\x98\x89'
        "zap:"$'\xE2\x9A\xA1\xEF\xB8\x8F'
    )
    message=$2

    case "$1" in
        list)
            for item in "${emojisList[@]}"
            do
                echo ":${item%%:*}: ${item#*:}"
            done
            return 0
            ;;
        comments)      emojis=( books ) ;;
        done)          emojis=( beers confetti_ball crown tada ) ;;
        build)         emojis=( hammer ) ;;
        deprecated)    emojis=( poop ) ;;
        fix)           emojis=( bug zap ambulance ) ;;
        localisation)  emojis=( globe_with_meridians ) ;;
        new)           emojis=( sparkles ) ;;
        merge)         emojis=( book ) ;;
        optimisation)  emojis=( racehorse ) ;;
        package)       emojis=( package ) ;;
        refactor)      emojis=( lipstick ) ;;
        tag)           emojis=( bookmark ) ;;
        testing)       emojis=( rotating_light ) ;;
        ticket)        emojis=( pencil ) ;;
        work)          emojis=( construction ) ;;
        *)             emojis=( smile simple_smile smirk blush wink sunglasses )
            message=$1
            ;;
    esac

    emojisLength=${#emojis[*]}
    emoji=":${emojis[$((RANDOM%emojisLength))]}:"
    message="$emoji $message"
    for item in "${emojisList[@]}"
    do
        key=${item%%:*}
        value=${item#*:}
        message=$(echo $message | sed -e "s/:$key:/$value/g")
    done

    git commit -a -m "$message"
}

alias ge='gitCommitEmotion'
alias gel='gitCommitEmotion list'
alias gec='gitCommitEmotion comments'
alias ged='gitCommitEmotion done'
alias geb='gitCommitEmotion build'
alias ged='gitCommitEmotion deprecated'
alias gef='gitCommitEmotion fix'
alias gelo='gitCommitEmotion localisation'
alias gen='gitCommitEmotion new'
alias gem='gitCommitEmotion merge'
alias geo='gitCommitEmotion optimisation'
alias gep='gitCommitEmotion package'
alias ger='gitCommitEmotion refactor'
alias getag='gitCommitEmotion tag'
alias gett='gitCommitEmotion testing'
alias get='gitCommitEmotion ticket'
alias gew='gitCommitEmotion work'

# vagrant
alias vu='vagrant up'
alias vs='vagrant ssh'

# docker
function dockerUp() {
	sudo ifconfig lo0 alias 10.254.254.254 255.255.255.0
    export DOCKER_XDEBUG_HOST=10.254.254.254
    cd /Users/math/Sites/bigyouth/by-docker-env
	docker volume create --name=by-sync
	docker-sync start
	docker-compose -f docker-compose.yml up -d
}
function dockerHalt() {
    cd /Users/math/Sites/bigyouth/by-docker-env
	docker-compose stop
	docker-sync stop
}
function dockerBuild() {
    cd /Users/math/Sites/bigyouth/by-docker-env
	docker-compose build
}
function dockerSsh() {
	docker exec -ti $1 zsh
}
alias du='dockerUp'
alias dh='dockerHalt'
alias db='dockerBuild'
alias ds='dockerSsh'

# scripts
alias harvest_coffee='/Users/math/Sites/darkwood/matyotools/php/Harvest/app/console harvest:coffee'
alias slack='/Users/math/Sites/darkwood/matyotools/php/Botman/bin/console'
