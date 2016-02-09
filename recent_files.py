import os, time, datetime;

def all_files_under(path):
    """Iterates through all files that are under the given path."""
    for cur_path, dirnames, filenames in os.walk(path):
        for filename in filenames:
            yield os.path.join(cur_path, filename)

edited_themes 		= max(all_files_under('../../themes/'), key = os.path.getmtime)
edited_themes_date 	= time.ctime(os.path.getmtime(edited_themes));
current_time 		= datetime.datetime.now();

# What is being printed in the file.
output_string 		= "Deployed at: %s - %s\n" % (edited_themes_date, edited_themes)

# Output a file with the date of change
with open("edited_files.txt", "a") as file_edited:
    file_edited.write(output_string)

# Write if there has been an update.