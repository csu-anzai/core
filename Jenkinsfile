pipeline {
    //env.PATH = "${tool 'Ant'}/bin:${env.PATH}"

    agent any
    stages {

        stage ('Kajona_Core_AdHoc_SQLite - Checkout') {
            steps {
                 checkout([$class: 'GitSCM', branches: [[name: '*/master']], doGenerateSubmoduleConfigurations: false, extensions: [[$class: 'RelativeTargetDirectory', relativeTargetDir: 'core']], submoduleCfg: [], userRemoteConfigs: [[url: 'https://github.com/artemeon/core.git']]])
            }
        }


        stage ('Kajona_Core_AdHoc_SQLite - Build') {
            steps {
                // Ant build step
                withEnv(["PATH+ANT=${tool 'Standard 1.9.x'}/bin"]) {
                        sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildSqliteFast "
                }
               // archiveArtifacts allowEmptyArchive: false, artifacts: 'core/_buildfiles/packages/', caseSensitive: true, defaultExcludes: true, fingerprint: false, onlyIfSuccessful: false
    		}
    	}


    }
}

