#!groovy
@Library('art-shared@master') _ 

pipeline {  
    agent any

    options { 
        checkoutToSubdirectory('core') 
    }

    triggers {
        pollSCM('H/5 * * * * ')
    }

    stages {

        stage('Prepare') {
            steps {

                dir('core/_buildfiles/build') {
                    deleteDir();
                }
                dir('core/_buildfiles/buildproject') {
                    deleteDir();
                }

                dir('core/_buildfiles/temp') {
                    deleteDir();
                }

                dir('core/_buildfiles/packages') {
                    deleteDir();
                }
                /*withAnt(installation: 'Ant') {
                    sh "ant -buildfile core/_buildfiles/build_jenkins.xml cleanFilesystem"
                }*/
            }
        }

        stage('Build Deps') {
            parallel {
                stage ('npm deps') {
                    steps {
                        withAnt(installation: 'Ant') {
                            sh "ant -buildfile core/_buildfiles/build_jenkins.xml installNpmBuildDependencies"
                        }
                    }
                }
                stage ('composer deps') {
                    steps {
                        withAnt(installation: 'Ant') {
                            sh "ant -buildfile core/_buildfiles/build_jenkins.xml installComposerBuildDependencies"
                        }
                    }
                }
            }
            
        }
    


        stage('build Project') {
            steps {
                withAnt(installation: 'Ant') {
                    sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildProject "
                    sh "ant -buildfile core/_buildfiles/build_jenkins.xml installProjectSqlite "
                }
            }
        }

        stage('testing') {
            parallel {
                stage ('lint') {
                    steps {
                        withAnt(installation: 'Ant') {
                            sh "ant -buildfile core/_buildfiles/build_jenkins.xml lint "
                        }
                    }
                }

                stage ('phpunit') {
                    steps {
                        withAnt(installation: 'Ant') {
                            sh "ant -buildfile core/_buildfiles/build_jenkins.xml phpunit "
                        }
                    }
                    post {
                        always {
                            junit 'core/_buildfiles/build/logs/junit.xml'
                        }
                    }
                }
                stage ('jasmine') {
                    steps {
                        withAnt(installation: 'Ant') {
                            sh "ant -buildfile core/_buildfiles/build_jenkins.xml jasmine "
                        }
                    }
                }
            }
        }
    

        stage ('Build Archive') {
            steps {
                // Ant build step
                withAnt(installation: 'Ant') {
                    sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildFullZip "
                }

                archiveArtifacts 'core/_buildfiles/packages/'
    		}
    	}

    }
    post {
        always {
            step([$class: 'Mailer', notifyEveryUnstableBuild: true, recipients: emailextrecipients([[$class: 'CulpritsRecipientProvider'], [$class: 'RequesterRecipientProvider']])])
            sendNotification currentBuild.result
        }
        
    }
}

