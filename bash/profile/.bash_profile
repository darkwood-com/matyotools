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

# git push to up-stream branch
function gitPushToUpStreamBranch() {
	if [ -z "$1" ]; then
		current_branch="$(git branch | grep '* ' | tr -d '* ')"
		git push --set-upstream origin $current_branch
	else
		current_branch="$(git branch | grep '* ' | tr -d '* ')"
		for var in "$@"
		do
			git checkout $var
			git push --set-upstream origin $var
		done
		git checkout $current_branch
	fi
}
alias gpushu='gitPushToUpStreamBranch'

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

# git push to current branch and merge and push to anoter one
function gitPushToBranchAndMerge() {
	if [ -z "$1" ]; then
		git push
	else
	    git push
		current_branch="$(git branch | grep '* ' | tr -d '* ')"
		for var in "$@"
		do
			git checkout $var
			git merge $current_branch
			git push
		done
		git checkout $current_branch
	fi
}
alias gpushm='gitPushToBranchAndMerge'

# git execute to branch
function gitExecuteToBranch() {
	current_branch="$(git branch | grep '* ' | tr -d '* ')"
	git checkout $1
	eval $2
	git checkout $current_branch
}
alias gx='gitExecuteToBranch'

# git emoji
emojiMap=(
	"" "all" "All" "smile simple_smile smirk blush wink sunglasses"
	"add" "addingADependency" "Adding a dependency" "heavy_plus_sign"
	"addtt" "addingATest" "Adding a test" "white_check_mark"
	"addci" "addingCiBuildSystem" "Adding CI build system" "construction_worker"
	"analytics" "analyticsOrTrackingCode" "Analytics or tracking code" "chart_with_upwards_trend"
	"b" "build" "Build" "hammer"
	"c" "comments" "Comments" "books"
	"conf" "configurationFiles" "Configuration files" "wrench"
	"ci" "continuousIntegration" "Continuous Integration" "green_heart"
	"cf" "criticalHotfix" "Critical hotfix" "ambulance"
	"d" "done" "Done" "beers confetti_ball crown tada"
	"de" "deprecated" "Deprecated" "poop"
	"deploy" "deployingStuff" "Deploying stuff" "rocket"
	"docker" "docker" "Docker" "whale"
	"doc" "documentation" "Documentation" "books"
	"docc" "documentingSourceCode" "Documenting source code" "bulb"
	"dw" "downgradingDependencies" "Downgrading dependencies" "arrow_down"
	"f" "fix" "Fix" "bug zap ambulance"
	"fl" "fixingOnLinux" "Fixing on Linux" "penguin"
	"fm" "fixingOnMacos" "Fixing on MacOS" "apple"
	"fw" "fixingOnWindows" "Fixing on Windows" "checkered_flag"
	"i" "init" "Initial commit" "tada"
	"im" "improveFormatStructure" "Improve formatStructure" "art"
	"lint" "lint" "Lint" "shirt"
	"lo" "localisation" "Localisation" "globe_with_meridians"
	"m" "merge" "Merging branches" "book"
	"meta" "metadata" "Metadata" "card_index"
	"n" "new" "New feature" "sparkles"
	"o" "optimisation" "Optimisation" "racehorse"
	"p" "package" "Package" "package"
	"r" "refactor" "Refactor" "lipstick"
	"rc" "refactorCode" "Refactor code" "hammer"
	"rmc" "removingCodeFiles" "Removing code or files" "fire"
	"rmd" "removingADependency" "Removing a dependency" "heavy_minus_sign"
	"s" "security" "Security" "lock"
	"t" "ticket" "Ticket" "pencil"
	"tag" "tag" "Version tag" "bookmark"
	"tt" "testing" "Testing" "rotating_light"
	"txt" "text" "Text" "pencil"
	"u" "generalUpdate" "General update" "zap"
	"up" "upgradingDependencies" "Upgrading dependencies" "arrow_up"
	"w" "work" "Work in progress" "construction"
)

function gitCommitEmotion() {
    # http://emoji-cheat-sheet.com/
    emojisList=(
        "ambulance:"$'\xF0\x9F\x9A\x91'
        "apple:"$'\xF0\x9F\x8D\x8F'
        "arrow_down:"$'\xE2\xAC\x87'
        "arrow_up:"$'\xE2\xAC\x86'
        "art:"$'\xF0\x9F\x8E\xA8'
        "beers:"$'\xF0\x9F\x8D\xBB'
        "blush:"$'\xF0\x9F\x98\x8A'
        "book:"$'\xF0\x9F\x93\x96'
        "bookmark:"$'\xF0\x9F\x94\x96'
        "books:"$'\xF0\x9F\x93\x9A'
        "bug:"$'\xF0\x9F\x90\x9B'
        "bulb:"$'\xF0\x9F\x92\xA1'
        "card_index:"$'\xF0\x9F\x93\x87'
        "chart_with_upwards_trend:"$'\xF0\x9F\x93\x88'
        "checkered_flag:"$'\xF0\x9F\x8F\x81'
        "confetti_ball:"$'\xF0\x9F\x8E\x8A'
        "construction:"$'\xF0\x9F\x91\xB7'
        "construction_worker:"$'\xF0\x9F\x91\xB7'
        "crown:"$'\xF0\x9F\x91\x91'
        "fire:"$'\xF0\x9F\x9A\x92'
        "globe_with_meridians:"$'\xF0\x9F\x8C\x90'
        "green_heart:"$'\xF0\x9F\x92\x9A'
        "hammer:"$'\xF0\x9F\x94\xA8'
        "heavy_minus_sign:"$'\xE2\x9E\x96'
        "heavy_plus_sign:"$'\xE2\x9E\x95'
        "lipstick:"$'\xF0\x9F\x92\x84'
        "lock:"$'\xF0\x9F\x94\x93'
        "package:"$'\xF0\x9F\x93\xA6'
        "pencil:"$'\xF0\x9F\x93\x9D'
        "penguin:"$'\xF0\x9F\x90\xA7'
        "poop:"$'\xF0\x9F\x92\xA9'
        "racehorse:"$'\xF0\x9F\x8F\x87'
        "rocket:"$'\xF0\x9F\x9A\x80'
        "rotating_light:"$'\xF0\x9F\x9A\xA8'
        "shirt:"$'\xF0\x9F\x8E\xBD'
        "simple_smile:"$'\xF0\x9F\x99\x82'
        "smile:"$'\xF0\x9F\x98\x80'
        "smirk:"$'\xF0\x9F\x98\x8F'
        "sparkles:"$'\xE2\x9C\xA8'
        "sunglasses:"$'\xF0\x9F\x98\x8E'
        "tada:"$'\xF0\x9F\x8E\x89'
        "whale:"$'\xF0\x9F\x90\xB3'
        "white_check_mark:"$'\xE2\x9C\x85'
        "wink:"$'\xF0\x9F\x98\x89'
        "wrench:"$'\xF0\x9F\x94\xA7'
        "zap:"$'\xE2\x9A\xA1\xEF\xB8\x8F'
    )
    message=$2

	if [ "$1" == "list" ]
	then
		message=""
		for (( i=0; i<=$(( ${#emojiMap[*]}-1 )); i+=4 ))
		do
			emojiMapList=( ${emojiMap[$i+3]} )
			emoji=$(printf ":%s:" "${emojiMapList[@]}")
			message="$message${emoji} : ge${emojiMap[$i]} => ${emojiMap[$i+2]} (gitCommitEmotion ${emojiMap[$i+1]})"
			if [ $(( $i+3 )) != $(( ${#emojiMap[*]}-1 )) ]
			then
			message="$message\\n"
			fi
		done
		
		for item in "${emojisList[@]}"
		do
			key=${item%%:*}
			value=${item#*:}
			message=$(echo $message | sed -e "s/:$key:/$value/g")
		done
		
		echo -e "$message"
		return 0
	fi
	
	for (( i=0; i<=$(( ${#emojiMap[*]}-1 )); i+=4 ))
	do
		if [ "$1" == "${emojiMap[$i+1]}" ]
		then
			emojis=( ${emojiMap[$i+3]} )
		fi
	done

    emojisLength=${#emojis[*]}
    emoji=":${emojis[$((RANDOM%emojisLength))]}:"
    message="$emoji $message"
    
    # temporary not replace emoji
    # for item in "${emojisList[@]}"
    # do
    #     key=${item%%:*}
    #     value=${item#*:}
    #     message=$(echo $message | sed -e "s/:$key:/$value/g")
    # done

    git commit -a -m "$message"
}

alias gel='gitCommitEmotion list'
for (( i=0; i<=$(( ${#emojiMap[*]} -1 )); i+=4 ))
do
alias ge${emojiMap[$i]}="gitCommitEmotion ${emojiMap[$i+1]}"
done

# vagrant
alias vu='vagrant up'
alias vs='vagrant ssh'

# docker
function dockerCleanBigyouth() {
    docker rm by_apache by_php-fpm
}
function dockerUp() {
    sudo ifconfig lo0 alias 10.254.254.254 255.255.255.0
    export DOCKER_XDEBUG_HOST=10.254.254.254
    (cd /Users/math/Sites/bigyouth/by-docker-env;docker volume create --name=by-sync)
    (cd /Users/math/Sites/bigyouth/by-docker-env;docker-sync start)
    (cd /Users/math/Sites/bigyouth/by-docker-env;docker-compose up -d)
}
function dockerHalt() {
    (cd /Users/math/Sites/bigyouth/by-docker-env;docker-compose stop)
    (cd /Users/math/Sites/bigyouth/by-docker-env;docker-sync stop)
}
function dockerBuild() {
    (cd /Users/math/Sites/bigyouth/by-docker-env;docker-compose build)
}
function dockerBuildRestart() {
    (cd /Users/math/Sites/bigyouth/by-docker-env;docker-compose stop)
    dockerBuild
    dockerCleanBigyouth
    export DOCKER_XDEBUG_HOST=10.254.254.254
    (cd /Users/math/Sites/bigyouth/by-docker-env;docker-compose up -d)
}
function dockerSsh() {
    docker exec -ti $1 ${2:-zsh}
}
function dockerSync() {
    (cd /Users/math/Sites/bigyouth/by-docker-env;docker-sync $@)
}
alias du='dockerUp'
alias dh='dockerHalt'
alias db='dockerBuild'
alias dbr='dockerBuildRestart'
alias ds='dockerSsh'
alias dsync='dockerSync'

# scripts
alias harvest_coffee='/Users/math/Sites/darkwood/matyotools/php/Harvest/app/console harvest:coffee'
alias slack='/Users/math/Sites/darkwood/matyotools/php/Botman/bin/console'
