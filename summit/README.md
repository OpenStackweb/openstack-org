# OpenStack Speaker Promo Code Ingestion Process

ingestion task sake dev/tasks/SummitSpeakerPromoCodesIngestTask

this task insert all promocodes by type on the specified summit

parameters
* promo_code_type: int [1:ACCEPTED , 2:ALTERNATE]
* promo_code_file: filename (csv file format) , file must be located under $ASSETS_PATH
* summit_id: int (summit identifier)


process task sake dev/SpeakerSelectionAnnouncementEmailSenderTask

this task sent all speaker announcement emails by summit

parameters

batch_size: int (process batch size, if not specified default 100)
summit_id: int (summit identifier)