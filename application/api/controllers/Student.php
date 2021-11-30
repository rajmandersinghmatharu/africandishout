<?php
require APPPATH.'libraries/REST_Controller.php';
class Student extends REST_Controller{
  public function __construct(){
    parent::__construct();
    $this->load->database();
    $this->load->model(array("api/student_model"));
    $this->load->library(array("form_validation"));
    $this->load->helper("security");
  }

  public function index_post() {
    $name = $this->security->xss_clean($this->input->post("name"));
    $email = $this->security->xss_clean($this->input->post("email"));
    $mobile = $this->security->xss_clean($this->input->post("mobile"));
    $course = $this->security->xss_clean($this->input->post("course"));

    $this->form_validation->set_rules("name", "Name", "required");
    $this->form_validation->set_rules("email", "Email", "required|valid_email");
    $this->form_validation->set_rules("mobile", "Mobile", "required");
    $this->form_validation->set_rules("course", "Course", "required");

    
    if($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => "All fields are needed"
      ) , REST_Controller::HTTP_NOT_FOUND);
    } else {
      if(!empty($name) && !empty($email) && !empty($mobile) && !empty($course)) {
        $student = array(
          "name" => $name,
          "email" => $email,
          "mobile" => $mobile,
          "course" => $course
        );

        if($this->student_model->insert_student($student)) {
          $this->response(array(
            "status" => 1,
            "message" => "Student has been created"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Failed to create student"
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }

    /*$data = json_decode(file_get_contents("php://input"));
    $name = isset($data->name) ? $data->name : "";
    $email = isset($data->email) ? $data->email : "";
    $mobile = isset($data->mobile) ? $data->mobile : "";
    $course = isset($data->course) ? $data->course : "";*/
  }
  public function index_put() {
    $data = json_decode(file_get_contents("php://input"));
    if(isset($data->id) && isset($data->name) && isset($data->email) && isset($data->mobile) && isset($data->course)) {
      $student_id = $data->id;
      $student_info = array(
        "name" => $data->name,
        "email" => $data->email,
        "mobile" => $data->mobile,
        "course" => $data->course
      );

      if($this->student_model->update_student_information($student_id, $student_info)) {
          $this->response(array(
            "status" => 1,
            "message" => "Student data updated successfully"
          ), REST_Controller::HTTP_OK);
      } else {
          $this->response(array(
          "status" => 0,
          "messsage" => "Failed to update student data"
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
      }
    } else {
          $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
          ), REST_Controller::HTTP_NOT_FOUND);
    }
  }

  public function index_delete() {
    $data = json_decode(file_get_contents("php://input"));
    $student_id = $this->security->xss_clean($data->student_id);

    if($this->student_model->delete_student($student_id)) {
      $this->response(array(
        "status" => 1,
        "message" => "Student has been deleted"
      ), REST_Controller::HTTP_OK);
    }else{
      // return false
      $this->response(array(
        "status" => 0,
        "message" => "Failed to delete student"
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }
  public function index_get() {
    $students = $this->student_model->get_students();
    if(count($students) > 0) {
      $this->response(array(
      "status" => 1,
      "message" => "Students found",
      "data" => $students
      ), REST_Controller::HTTP_OK);
    } else {
        $this->response(array(
        "status" => 0,
        "message" => "No Students found",
        "data" => $students
        ), REST_Controller::HTTP_NOT_FOUND);
    }
  }
}
?>