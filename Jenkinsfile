pipeline {

    //def labels = ['php7', 'mssql']
    //def builders = [:]

    agent {
        any
    }

    //triggers {
        //cron('H */4 * * 1-5')
        //pollSCM('H/5 * * * * ')
    //}


    //for (x in labels) {

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
                //step([$class: 'XUnitBuilder', testTimeMargin: '3000', thresholdMode: 1, thresholds: [[$class: 'FailedThreshold', failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0'], [$class: 'SkippedThreshold', failureNewThreshold: '1000', failureThreshold: '1000', unstableNewThreshold: '1000', unstableThreshold: '1000']], tools: [[$class: 'JUnitType', deleteOutputFiles: true, failIfNotNew: true, pattern: 'core/_buildfiles/build/logs/junit.xml', skipNoTestFiles: false, stopProcessingIfError: true]]])
            }
        }

    	stage ('Archive') {
    	    steps {
    	        archiveArtifacts 'core/_buildfiles/packages/'
    	    }
    	}

    	stage ('Mailer') {
    	    steps {
    	        step([$class: 'Mailer', notifyEveryUnstableBuild: true, recipients: '', sendToIndividuals: true])
    	    }
    	}


    }
}

