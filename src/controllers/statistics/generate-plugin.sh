#!/bin/bash
name=$1
file=$1".php"
echo "Generating plugin $name to $file"

# Frame and docs
echo "<?php" >> $file
echo "/*" >> $file
echo " * TODO: Documentation for $name" >> $file
echo " */" >> $file

# Class and method
echo "class $name implements IStatistic" >> $file
echo "{" >> $file 
echo "  public function evaluate(\$session)" >> $file
echo "  {" >> $file
echo "    \$result = new Statistic();" >> $file
echo "    \$result->name = \"$name\";" >> $file
echo "    \$result->type = \"numeric\"; // Set type" >> $file
echo "    return \$result;" >> $file
echo "  }" >> $file
echo "}" >> $file

# Return instance
echo "" >> $file
echo "return new $name();" >> $file
