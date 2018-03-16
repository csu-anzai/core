pipeline {
    agent any
    stages {
        stage('build') {
            steps {

                def antVersion = 'Ant'
                withEnv( ["ANT_HOME=${tool antVersion}"] ) {
                    sh '$ANT_HOME/bin/ant -file core/_buildfiles/build_jenkins.xml buildSqliteFast'
                }
                //echo 'Starting Build'
                //env.PATH = "${tool 'Ant'}/bin:${env.PATH}"
                //checkout scm
                //sh 'ant -file core/_buildfiles/build_jenkins.xml buildSqliteFast'
            }
        }
    }
}