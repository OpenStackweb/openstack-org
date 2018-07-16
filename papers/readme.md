# RUN POT file Generator for a paper

sake dev/tasks/PaperContentPOTFileGeneratorTask paper_id=123

output pot file should be under
"papers/pot" path

# compile po files to mo

sake dev/tasks/CompilePO2MOTask module=papers

