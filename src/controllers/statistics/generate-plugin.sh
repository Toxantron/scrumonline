#!/bin/bash
name=$1
file=$1".php"
echo "Generating plugin $name to $file"

# Frame and docs
echo "<?php" >> $file
echo "/*" >> $file
echo " * TODO: Documentation for $name" >> $file
echo " */" >> $file

# Class header
echo "class $name implements IStatistic" >> $file
echo "{" >> $file 

# getType method
echo "  public function getType()" >> $file
echo "  {" >> $file
echo "    return \"numeric\"; // Define type" >> $file
echo "  }" >> $file
echo "" >> $file

# evaluate method
echo "  public function evaluate(\$session)" >> $file
echo "  {" >> $file
echo "    return 0;" >> $file
echo "  }" >> $file

# Footer and return instance
echo "}" >> $file
echo "" >> $file
echo "return new $name();" >> $file
