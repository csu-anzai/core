pipeline {
    agent any
    stages {
        stage('build') {
            steps {


                //echo 'Starting Build'
                //env.PATH = "${tool 'Ant'}/bin:${env.PATH}"
                checkout scm
                sh 'ant -file core/_buildfiles/build_jenkins.xml buildSqliteFast'
            }
        }
    }
}