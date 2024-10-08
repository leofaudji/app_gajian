<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Btools extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->input->is_cli_request() or exit("Execute via CLI: php index.php migrate");
    }

    public function help(){
        $result = "The following are the available command line interface commands\n\n";
        $result.= "php index.php tools migration \"file_name\"         Create new migration file\n";
        $result.= "php index.php tools migrate [\"version_number\"]    Run all migrations. The version number is optional.\n";

        echo $result . PHP_EOL;
    }

    public function migrate($version = null) {
        $this->load->library('migration');

        if ($version != null) {
            if ($this->migration->version($version) === FALSE) {
                show_error($this->migration->error_string());
            } else {
                echo "Migrations run successfully" . PHP_EOL;
            }
            return;
        }

        if ($this->migration->latest() === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo "Migrations run successfully" . PHP_EOL;
        }
    }

    public function migration($name) {
        $this->make_migration_file($name);
    }

    protected function make_migration_file($name){
        $date = new DateTime();
        $timestamp = $date->format('YmdHis');

        $table_name = strtolower($name);

        $path = FCPATH . "bmigrations/$timestamp" . "_" . "$name.php";

        $my_migration = fopen($path, "w") or die("Unable to create migration file!");

        $migration_template = "<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - https://ilmion.com بسم الله الرحمن الرحيم ');

class Migration_$name extends CI_Migration {

    public function up() {
        // Create New Scheme Database (Please Read Migration Codeigniter)
    }

    public function down() {
        // Rollback New Scheme
    }

}";

        fwrite($my_migration, $migration_template);

        fclose($my_migration);

        echo "$path migration has successfully been created." . PHP_EOL;
    }
}