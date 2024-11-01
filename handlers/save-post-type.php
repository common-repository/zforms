<?php

    //ini_set('display_errors', 1);

    function zfHandle($formData, $formKey) {

        //let user submit multiple times
        $_SESSION['zforms']['handled'][$formKey] = 0;

        // post_title
        $postTitle = 'use post_title as form input';
        if (isset($formData['post_title'])) { $postTitle = $formData['post_title']; }

        // post_content
        $postContent = '<p>use post_content as form_input</p>';
        if (isset($formData['post_content'])) { $postContent = $formData['post_content']; }

        // post_category
        $postCategory = ''; //post_category
        if (isset($formData['post_category'])) { $postCategory = $formData['post_category']; }

        // tags_input
        $tagsInput = '';
        if (isset($formData['tags_input'])) { $tagsInput = $formData['tags_input']; }

        // post_status
        $postStatus = 'publish'; // publish, preview, future, etc.
        if (isset($formData['post_status'])) { $postStatus = $formData['post_status']; }

        // post_type
        $postType = 'post';
        if (isset($formData['post_type'])) { $postType = $formData['post_type']; }

        $postAuthor = 1;

        $postDate = date('Y-m-d G:i:s');

        $postData = array(
            'post_title'	=> $postTitle,
            'post_content' => $postContent,
            'post_status' => $postStatus, 
            'post_type' => $postType,
            'post_author' => $postAuthor,
            'post_date' => $postDate,
        );

        $postModel = ZFactory::createModel('posts');
        $postId = $postModel->insert($postData);

        //die("postId: $postId , data:\n" . print_r($postData,1));

        if (!$postId) { 
            return 0; 
        }

        //strip the main stuff
        $metaData = array();
        foreach($postData as $key => $value) {
            if (!isset($postData[$key])) {
                $metaData[$key] = $value;
            }
        }

        if (!empty($metaData)) {
            $postMetaModel = ZFactory::createModel('postmeta');
            //save everything else as a meta value
            foreach($metaData as $metaKey => $metaValue) {
                //add_post_meta($postId, $metaKey, $metaValue);
                $postMetaData = array(
                    'post_id' => $postId,
                    'meta_key' => $metaKey,
                    'meta_value' => $metaValue,
                );
                $postMetaId = $postMetaModel->insert($postMetaData);
            }
        }

        if ($postId > 0) {
            return true; //run zfSuccess
        }
        return false; //run zfError
    }

    function zfError() {
        return 0; //don't send email
    }

    function zfSuccess() {
        return 0; //don't send email
    }