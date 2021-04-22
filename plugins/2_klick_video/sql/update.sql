ALTER TABLE `XXX_plugin_2_klick_video`
	ADD COLUMN use_flex_video BOOLEAN NOT NULL DEFAULT TRUE,
	ADD COLUMN thumbnail_sizes VARCHAR(255) NOT NULL DEFAULT '(max-width: 40.063em) 99vw, 70vw'
; ##b_dump##

ALTER TABLE `XXX_plugin_2_klick_video_lang`
	ADD COLUMN confirm_button_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#A44F21',
	ADD COLUMN dismiss_button_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#FFFFFF',
	ADD COLUMN confirm_text_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#FFFFFF',
	ADD COLUMN dismiss_text_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#1B93B9'
; ##b_dump##
