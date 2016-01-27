<?php
	$media = mpp_get_current_media();	
	if( ! $media )
		return ;

	$src = mpp_get_media_src( '', $media );

	$ext = mpp_get_file_extension( $src );

	if( $ext ){
		//$ext = strtolower( $ext );
			//for doc viewer, we will use google doc viewer for 

		//IF IT IS PDF, PPT OR TIFF USE THE GOOGLE VIEWER
		$url = "http://docs.google.com/viewer?url=". urlencode( $src );
		//should we validate if the type is supported by viewer?
		//see for supported type
		//https://support.google.com/drive/answer/2423485?hl=en&p=docs_viewer&rd=1
		//and check this for more details
		//https://docs.google.com/viewer

		$html = "<iframe src='" . $url."&embedded=true' style='border: none;'></iframe>";
	}

	echo $html;