# RUN POT file Generator for a paper

sake dev/tasks/PaperContentPOTFileGeneratorTask paper_id=123

output pot file should be under
"papers/pot" path

# compile po files to mo

sake dev/tasks/CompilePO2MOTask module=papers

# PDF rendering

sudo apt install xvfb
sudo apt install wkhtmltopdf

sudo apt-get install language-pack-ja
sudo apt-get install japan*

sudo apt-get install language-pack-zh*
sudo apt-get install chinese*

sudo apt-get install language-pack-ko
sudo apt-get install korean*

