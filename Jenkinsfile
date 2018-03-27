#!groovy
@Library('art-shared@master') _ 


//working as expected, but limited capabilities
//defaultBuild antBuildTask: 'installProjectSqlite', buildNode: 'php7', checkoutDir: 'core'



pipeline {  
        agent none

        options { 
            checkoutToSubdirectory('core') 
        }

        triggers {
            pollSCM('H/5 * * * * ')
        }

        stages {

            stage('Build') {
                parallel {
                    stage ('slave php7') {
                        agent {
                            label 'php7'
                        }
                        steps {
                            withAnt(installation: 'Ant') {
                                sh "ant -buildfile core/_buildfiles/build_jenkins.xml installProjectSqlite"
                            }
                            archiveArtifacts 'core/_buildfiles/packages/'
                        }
                    }

                    stage ('slave mssql') {
                        agent {
                            label 'mssql'
                        }
                        steps {
                            withAnt(installation: 'Ant') {
                                sh "ant -buildfile core/_buildfiles/build_jenkins.xml installProjectSqlite"
                            }
                            archiveArtifacts 'core/_buildfiles/packages/'
                        }
                    }
                }
                
            }

        }
        post {
            always {
                step([$class: 'Mailer', notifyEveryUnstableBuild: true, recipients: emailextrecipients([[$class: 'CulpritsRecipientProvider'], [$class: 'RequesterRecipientProvider']])])
                //sendNotification currentBuild.result
            }
            
        }
    }