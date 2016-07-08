# Scrum Poker Statistics
This directory contains statistics of the session. Each statistic is implemented by a dedicated implementation of the `IStatistic` interface.
Create new plugins by navigating to the directory and running:

```sh
$ bash generate-plugin.sh PluginName
```

Please **do not** change the name in `getName()`. It is used throughout the application to filter instances or link to files. Make sure to 
provide a meaningful comment on top of the class, because the application links directly to the source file from the UI.
