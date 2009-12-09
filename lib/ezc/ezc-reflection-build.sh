#!/bin/bash
cd trunk

echo "Creating autoload environment for 'trunk':";
if test -d autoload; then
    echo "Autoload directory exists."
else
    echo "Creating missing 'autoload' directory."
    mkdir autoload
fi

for i in */src/*autoload.php; do
    p=`echo $i | cut -d / -f 1`;
    r=`echo $i | cut -d / -f 2`;
    b=`echo $i | cut -d / -f 3`;

    if test ! $p == "autoload"; then
        if test ! $r == "releases"; then
            if test -L autoload/$b; then
                echo "Symlink for $b to $i exists."
            else
                echo "Creating symlink from $i to autoload/$b."
                ln -s "../$i" "autoload/$b"
            fi
        fi
    fi
done

mkdir -p reports
#UnitTest/src/runtests.php --coverage-html reports/coverage --coverage-clover reports/clover.xml --log-junit reports/log-junit.xml --log-pmd reports/pmd.xml Reflection
UnitTest/src/runtests.php Reflection
