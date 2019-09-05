<?php 
$config = array(
    'admin/save' => array(
        array(
            'field' => 'full_name',
            'label' => 'Name',
            'rules' => 'trim|required|valid_name'
        ),
        array(
            'field' => 'user_type',
            'label' => 'Role',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'trim|required|unique_key[admin.username]'
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|valid_email|unique_key[admin.email]'
        ),
        array(
            'field' => 'mobile',
            'label' => 'Mobile',
            'rules' => 'trim|valid_mobile'
        )
    ),

    'admin/modify' => array(
        array(
            'field' => 'full_name',
            'label' => 'Name',
            'rules' => 'trim|required|valid_name'
        ),
        array(
            'field' => 'user_type',
            'label' => 'Role',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'trim|required|unique_key[admin.username.user_id.'. (int) $this->input->post('user_id') .']'
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim'
        ),
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|trim|valid_email|unique_key[admin.email.user_id.'. (int) $this->input->post('user_id') .']'
        ),
        array(
            'field' => 'mobile',
            'label' => 'Mobile',
            'rules' => 'trim|valid_mobile'
        )
    ),
);


