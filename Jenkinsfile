pipeline {
    env.PATH = "${tool 'Ant'}/bin:${env.PATH}"

    agent any
    stages {

        stage ('Kajona_Core_AdHoc_SQLite - Checkout') {
         	 checkout scm
        }


        stage ('Kajona_Core_AdHoc_SQLite - Build') {
            // Ant build step
            withEnv(["PATH+ANT=${tool 'Standard 1.9.x'}/bin"]) {
     			if(isUnix()) {
     				sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildSqliteFast "
    			} else {
     				bat "ant -buildfile core/_buildfiles/build_jenkins.xml buildSqliteFast "
    			}
     		}
    		archiveArtifacts allowEmptyArchive: false, artifacts: 'core/_buildfiles/packages/', caseSensitive: true, defaultExcludes: true, fingerprint: false, onlyIfSuccessful: false
    	}


    }
}



/*

timestamps {

node () {

	stage ('Kajona_Core_AdHoc_SQLite - Checkout') {
 	 checkout([$class: 'GitSCM', branches: [[name: '*/master']], doGenerateSubmoduleConfigurations: false, extensions: [], submoduleCfg: [], userRemoteConfigs: [[credentialsId: '', url: 'https://github.com/artemeon/core.git']]])
	}
	stage ('Kajona_Core_AdHoc_SQLite - Build') {
 			// Ant build step
	withEnv(["PATH+ANT=${tool 'Standard 1.9.x'}/bin"]) {
 			if(isUnix()) {
 				sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildSqliteFast "
			} else {
 				bat "ant -buildfile core/_buildfiles/build_jenkins.xml buildSqliteFast "
			}
 		}
		archiveArtifacts allowEmptyArchive: false, artifacts: 'core/_buildfiles/packages/', caseSensitive: true, defaultExcludes: true, fingerprint: false, onlyIfSuccessful: false
	}
	stage ('Kajona_Core_AdHoc_SQLite - Post build actions') {

		// Artifact Archiver
// Unable to convert a post-build action referring to "hudson.plugins.plot.PlotPublisher". Please verify and convert manually if required.
// Unable to convert a post-build action referring to "xunit". Please verify and convert manually if required.
		// Mailer notification
		step([$class: 'Mailer', notifyEveryUnstableBuild: false, recipients: '', sendToIndividuals: true])

// Unable to convert a post-build action referring to "hudson.plugins.jabber.im.transport.JabberPublisher". Please verify and convert manually if required.
// Unable to convert a post-build action referring to "org.jenkinsci.plugins.github.status.GitHubCommitStatusSetter". Please verify and convert manually if required.
	}
}
}

*/