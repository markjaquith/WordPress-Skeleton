@echo off
where /q lessc || (
    echo You must install lessc: npm install -g less
    goto :eof
)
lessc --yui-compress timely-bootstrap.less ..\css\bootstrap.min.css