<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Course extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
    }

    function index() {
        if (!$this->rbac->hasPrivilege('course', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Academics');
        $this->session->set_userdata('sub_menu', 'Academics/course');
        $data['title'] = 'Add course';
        $course_result = $this->course_model->get();
        $data['courselist'] = $course_result;
        $data['course_types'] = $this->customlib->courseType();
        $this->form_validation->set_rules('name', $this->lang->line('course_name'), 'trim|required|xss_clean|callback__check_name_exists');
        $this->form_validation->set_rules('type', $this->lang->line('type'), 'trim|required|xss_clean');
        if ($this->input->post('code')) {
            $this->form_validation->set_rules('code', $this->lang->line('code'), 'trim|required|callback__check_code_exists');
        }
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/course/coursesList', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'type' => $this->input->post('type'),
            );
            $this->course_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/course/index');
        }
    }

    function view($id) {
        if (!$this->rbac->hasPrivilege('course', 'can_view')) {
            access_denied();
        }
        $data['title'] = 'Course List';
        $course = $this->course_model->get($id);
        $data['course'] = $course;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/course/coursesShow', $data);
        $this->load->view('layout/footer', $data);
    }

    function delete($id) {
        if (!$this->rbac->hasPrivilege('course', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Course List';
        $this->course_model->remove($id);
        redirect('admin/course/index');
    }

    function _check_name_exists() {
        $data['name'] = $this->security->xss_clean($this->input->post('name'));
        if ($this->course_model->check_data_exists($data)) {
            $this->form_validation->set_message('_check_name_exists', $this->lang->line('name_already_exists'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function _check_code_exists() {
        $data['code'] = $this->security->xss_clean($this->input->post('code'));
        if ($this->course_model->check_code_exists($data)) {
            $this->form_validation->set_message('_check_code_exists', $this->lang->line('code_already_exists'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function edit($id) {
        if (!$this->rbac->hasPrivilege('course', 'can_edit')) {
            access_denied();
        }
        $course_result = $this->course_model->get();
        $data['courselist'] = $course_result;
        $data['title'] = 'Edit Course';
        $data['id'] = $id;
        $course = $this->course_model->get($id);
        $data['course'] = $course;
        $data['course_types'] = $this->customlib->courseType();
        $this->form_validation->set_rules('name', $this->lang->line('course'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/course/coursesEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id' => $id,
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'type' => $this->input->post('type'),
            );
            $this->course_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/course/index');
        }
    }

    function getSubjctByClassandSection() {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $date = $this->teachercourse_model->getCourseByClsandSection($class_id, $section_id);
        echo json_encode($data);
    }

}

?>