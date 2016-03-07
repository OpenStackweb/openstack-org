# OpenStack Speaker Promo Code Ingestion Process

ingestion task 

sake /SummitSpeakerPromoCodesIngestTask promo_code_type=2 promo_code_file='speakers_promo_codes_alternates.csv' summit_id=6

this task insert all promocodes by type on the specified summit

parameters
* promo_code_type: int [1:ACCEPTED , 2:ALTERNATE]
* promo_code_file: filename (csv file format) , file must be located under $ASSETS_PATH
* summit_id: int (summit identifier)


process task 

sake SpeakerSelectionAnnouncementEmailSenderTask summit_id=6 batch_size=10

this task sent all speaker announcement emails by summit

parameters

batch_size: int (process batch size, if not specified default 100)
summit_id: int (summit identifier)