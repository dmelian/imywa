### List frame config. 
Save the bas\_list_frame configs. (Header widths, order and visibility).
The widths and order have been done, remain the visibility.

### List frame height.
Dinamically change the height of the bas_list_frame and adjust the visible lines count. 

### List frame sorting.
Enable column sorting by dobleclicking on the column header.

### List frame multiline cell.
Enable multiline rows, rows with more than one line. (Same height for each row).
Something similar to Imna's textareas.

### List Frame.
Column Resize. When a column is resized, also the others column's size are changed.  

### Mayfly forms. 
The forms opened from the dashboard, must not be permanently save on the form's stack (bread crumb). 
If a second form is opened from the dashboard, this must replace the previous one. 
They can look diferent on the bread-crumb. 

### Parser error alert.
Some times, the jscommand response to a http request has a json parser error, don't fit into the browser alert dialog. 
Put this one in a specific div where it fit complete.

### Datapointer. Store into indexed files.
The random access file is not a solution to store a query result in php because of the way php store data types.
Then another solution is to use an indexed file with blocks of 4k.

### Frame action.
How are the frame actions used?.
This is code on bas\_frmx_form.OnAction.
If the frameid is defined the OnAction is called. This was coded for the old ajax process, whe imywa was managed by submits.
This must be changed.

### Debugging.
For a better debug. A history of open forms and actions request and responses with their data, will be stored to watch to them.

### 2017
Simplify the old code to use in a modern form.
