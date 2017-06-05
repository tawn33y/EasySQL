# Contributing to the project
EasySQL is an open source software project and we encourage developers to contribute patches and code for us to include in the main package of EasySQL. All contributions will be fully **credited**.

## Comment your code
Your code should be as self-documenting as possible, but because this is an open source project with multiple contributors please add comments whenever possible.

### Comment on individual lines
You do not need to comment on everything you do, but if you make a decision that could be confusion or something could be potentially seen as an error (e.g. because it is not the default way or not the most obvious way) please comment on why you did this. This prevents people from “fixing” stuff that is not broken and maybe breaking things because of this.

### Keep lines as short as possible (max. 80 characters)
Keeping your lines short makes it much more easy to spot errors and for other developers to scan the code.

Keeping to an 80 character limit makes you think more about how to code something and often forces you to refactor and simplify your code.

Lastly, less character per line, mean less potential merge conflicts.

### Reduce parameters (max. 3)
Never use more than 3 parameters, this will keep you from falling into bad habits. If you need complex configuration (which you should try to avoid), use an object.

### Reduce nesting depth (max. 3)
Do not nest to deeply. This will make the code confusing, hard to read and again, make merging hard.
If your code gets to complex, try to refactor parts out into individual functions.
