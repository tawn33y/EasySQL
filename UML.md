| files                                                 |
| :---------------------------------------------------- |
| read_files()                                          |
| write_to_file()                                       |
| append_to_json_file()                                 |


| misc                                                  |
| :---------------------------------------------------- |
| std_output()                                          |
| throw_error()                                         |
| throw_error_from_error_object()                       |


| args_validations                                      |
| :---------------------------------------------------- |
| validate_arg_isset()                                  |
| validate_arg_is_string()                              |
| validate_arg_is_boolean()                             |
| validate_arg_is_array()                               |
| validate_arg_is_string_iff_isset()                    |
| validate_arg_is_array_iff_isset()                     |
| validate_arg_array_has_required_number_of_keys()      |


| args_formatting                                       |
| :---------------------------------------------------- |
| convert_array_to_string()                             |





| init                                                  |
| :---------------------------------------------------- |
| $backtrace_enable                                     |
|                                                       |
| use files                                             |
| use misc                                              |
| use args_validations                                  |
| use args_formatting                                   |
|                                                       |
| __destruct()                                          |
| set_backtrace_enable()                                |
| get_backtrace_enable()                                |




| credentials                                           |
| :---------------------------------------------------- |
| $database_type                                        |
| $host_name                                            |
| $host_username                                        |
| $host_password                                        |
| $database                                             |
|                                                       |
| __destruct()                                          |
| set_database_type()                                   |
| get_database_type()                                   |
| set_host_name()                                       |
| get_host_name()                                       |
| set_host_username()                                   |
| get_host_username()                                   |
| set_host_password()                                   |
| get_host_password()                                   |
| set_database()                                        |
| get_database()                                        |
| set_credentials()                                     |
| set_credentials_via_json_file()                       |
| get_credentials()                                     |
| validate_isset_credentials()                          |
| validate_credentials_args()                           |





| crud                                                  |
| :---------------------------------------------------- |
| $table                                                |
| $data_1                                               |
| $data_2                                               |
|                                                       |
| __destruct()                                          |
| set_table()                                           |
| get_table()                                           |
| set_data_1()                                          |
| get_data_1()                                          |
| set_data_2()                                          |
| get_data_2()                                          |
| set_crud()                                            |
| get_crud()                                            |
| _read()                                               |
| _create()                                             |
| _update()                                             |
| _delete()                                             |
| _alter()                                              |





| logs                                                  |
| :---------------------------------------------------- |
| $logs_enable                                          |
| $logs_file_path                                       |
| $log_minify                                           |
|                                                       |
| __destruct()                                          |
| set_logs_enable()                                     |
| get_logs_enable()                                     |
| set_logs_file_path()                                  |
| get_logs_file_path()                                  |
| set_logs_minify()                                     |
| get_logs_minify()                                     |
| create_error_log()                                    |
| get_logs()                                            |
| clear_logs()                                          |





| connection                                            |
| :---------------------------------------------------- |
| $connection_object                                    |
|                                                       |
| __destruct()                                          |
| set_connection_object()                               |
| get_connection_object()                               |
| open_connection()                                     |
| close_connection()                                    |





| easysql                                               |
| :---------------------------------------------------- |
| __destruct()                                          |
| select()                                              |
| select2()                                             |
| insert()                                              |
| update()                                              |
| delete()                                              |
| alter()                                               |





| prepare_all_methods                                   |
| :---------------------------------------------------- |
| $table                                                |
| $method_name                                          |
|                                                       |
| __destruct()                                          |
| set_method_name()                                     |
| get_method_name()                                     |
| set_table()                                           |
| get_table()                                           |





| prepare_select                                        |
| :---------------------------------------------------- |
| $select_data                                          |
| $select_query                                         |
| $select_order                                         |
|                                                       |
| __destruct()                                          |
| set_select_data()                                     |
| get_select_data()                                     |
| set_select_query()                                    |
| get_select_query()                                    |
| set_select_order()                                    |
| get_select_order()                                    |





| prepare_select2                                       |
| :---------------------------------------------------- |
| set_select_query()                                    |
| get_select_query()                                    |





| prepare_insert                                        |
| :---------------------------------------------------- |
| $insert_data                                          |
|                                                       |
| __destruct()                                          |
| set_insert_data()                                     |
| get_insert_data()                                     |





| prepare_update                                        |
| :---------------------------------------------------- |
| $update_data                                          |
| $update_query                                         |
|                                                       |
| __destruct()                                          |
| set_update_data()                                     |
| get_update_data()                                     |
| set_update_query()                                    |
| get_update_query()                                    |





| prepare_delete                                        |
| :---------------------------------------------------- |
| $delete_data                                          |
|                                                       |
| __destruct()                                          |
| set_delete_data()                                     |
| get_delete_data()                                     |





| prepare_alter                                         |
| :---------------------------------------------------- |
| $alter_data                                           |
| $alter_operand                                        |
|                                                       |
| __destruct()                                          |
| set_alter_operand()                                   |
| get_alter_operand()                                   |
| set_alter_data()                                      |
| get_alter_data()                                      |
