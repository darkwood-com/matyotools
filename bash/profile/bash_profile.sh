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
GIT_AUTHOR_EMAIL="matyo91@gmail.com"
GIT_COMMITTER_EMAIL="$GIT_AUTHOR_EMAIL"
GIT_MERGE_AUTOEDIT=no
# git config --global user.name "$GIT_AUTHOR_NAME"
# git config --global user.email "$GIT_AUTHOR_EMAIL"

alias gs='git status'
alias gd='git diff'
alias gc='git commit -a -m'
alias gpull='git pull'
alias gpush='git push'
alias gup='git up'

# git emoji
function gitCommitEmotion() {
    # http://emoji-cheat-sheet.com/
    emojisList=(
        "smile:"$'\xF0\x9F\x98\x80'
        "simple_smile:"$'\xF0\x9F\x99\x82'
        "smirk:"$'\xF0\x9F\x98\x8F'
        "blush:"$'\xF0\x9F\x98\x8A'
        "wink:"$'\xF0\x9F\x98\x89'
        "sunglasses:"$'\xF0\x9F\x98\x8E'
        "bug:"$'\xF0\x9F\x90\x9B'
        "zap:"$'\xE2\x9A\xA1\xEF\xB8\x8F'
        "pencil:"$'\xF0\x9F\x93\x9D'
        "book:"$'\xF0\x9F\x93\x96'
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
        bug)    emojis=( bug ) ;;
        ticket) emojis=( pencil ) ;;
        merge)  emojis=( book ) ;;
        *)      emojis=( smile simple_smile smirk blush wink sunglasses )
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
alias geb='gitCommitEmotion bug'
alias get='gitCommitEmotion ticket'
alias gem='gitCommitEmotion merge'

# vagrant
alias vu='vagrant up'
alias vs='vagrant ssh'
