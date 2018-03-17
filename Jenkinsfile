pipeline {
    //env.PATH = "${tool 'Ant'}/bin:${env.PATH}"

    agent any
    stages {

        stage ('Git Checkout') {
            steps {
                 checkout([$class: 'GitSCM', branches: [[name: '*/master']], doGenerateSubmoduleConfigurations: false, extensions: [[$class: 'RelativeTargetDirectory', relativeTargetDir: 'core']], submoduleCfg: [], userRemoteConfigs: [[url: 'https://github.com/artemeon/core.git']]])
            }
        }


        stage ('Build') {
            steps {
                // Ant build step
                //withEnv(["PATH+ANT=${tool 'Standard 1.9.x'}/bin"]) {
                withAnt(installation: 'Ant') {
                    sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildSqliteFast "
                }

    		}
    	}

        stage ('Publish xUnit') {
            steps {
                junit 'core/_buildfiles/build/logs/junit.xml'
            }
        }

    	stage ('Archive') {
    	    steps {
    	        archiveArtifacts 'core/_buildfiles/packages/'
    	    }
    	}


    }
}

