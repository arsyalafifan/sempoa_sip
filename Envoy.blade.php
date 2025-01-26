@servers(['web' => 'riqcomco@202.73.25.70 -p 2425'])
@setup
    $branch = 'development';
    $repository = 'git@gitlab.com:andogates/mi-bedil.git';
    $releases_dir = '/usr/share/nginx/html';
    $app_dir = '/home/riqcomco/public_html/mibedil.riqcom.co.id';
    $release = date('dmYHis');
    $new_release_dir = $releases_dir .'/newrelease/mibedil/'. $release;

    function logMessage($message) {
return "echo '\033[32m" .$message. "\033[0m';\n";
}
@endsetup
@story('deploy')
    startDeployment
@endstory

@story('deploy_prod')
    startDeploymentProd
@endstory

@task('startDeployment')
    {{ logMessage("🏃  Starting deployment…") }}
    cd {{ $app_dir }}
    git checkout {{ $branch }}
    git pull origin {{ $branch }}
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
@endtask

@task('startDeploymentProd')
    {{ logMessage("🏃  Starting deployment Production…") }}
    cd /usr/share/nginx/html/mibedil
    git checkout master
    git pull origin master
    php artisan migrate --force
    php artisan cache:clear
@endtask

@task('cloneRepository')
    {{ logMessage("🌀  Cloning repository…") }}
    [ -d {{ $releases_dir }} ] || mkdir {{ $releases_dir }}
    git clone --depth 1 {{ $repository }} {{ $new_release_dir }}
    echo 'Done'
@endtask
@task('run_composer')
    echo "Starting deployment ({{ $release }})"
    cd {{ $new_release_dir }}
    composer install --prefer-dist --no-scripts -q -o
    echo 'Done'
@endtask
@task('update_symlinks')
    echo "Linking storage directory"
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage
    echo 'Done'    echo 'Linking .env file'
    ln -nfs {{ $app_dir }}/.env {{ $new_release_dir }}/.env
    echo 'Done'    echo 'Linking current release'
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}/current
    echo 'Done'
@endtask

