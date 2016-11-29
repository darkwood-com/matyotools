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

# git
GIT_AUTHOR_NAME="Mathieu Ledru"
GIT_COMMITTER_NAME="$GIT_AUTHOR_NAME"
git config --global user.name "$GIT_AUTHOR_NAME"
GIT_AUTHOR_EMAIL="matyo91@gmail.com"
GIT_COMMITTER_EMAIL="$GIT_AUTHOR_EMAIL"
git config --global user.email "$GIT_AUTHOR_EMAIL"
GIT_MERGE_AUTOEDIT=no

alias gs='git status'
alias gd='git diff'
alias gc='git commit -a -m'
alias gpull='git pull'
alias gpush='git push'
alias gup='git up'

# vagrant
alias vu='vagrant up'
alias vs='vagrant ssh'

# domino
alias harvest_domino='/Users/math/Sites/darkwood/matyotools/php/Harvest/app/console harvest:domino'