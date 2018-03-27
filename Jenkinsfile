#!groovy
@Library('art-shared@master') _ 


//working as expected, but limited capabilities
//defaultBuild antBuildTask: 'installProjectSqlite', buildNode: 'php7', checkoutDir: 'core'



pipeline {  
        agent none

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
                            checkout([
                                $class: 'GitSCM', branches: scm.branches, extensions: [[$class: 'RelativeTargetDirectory', relativeTargetDir: 'core']], userRemoteConfigs: scm.userRemoteConfigs
                            ])

                            withAnt(installation: 'Ant') {
                                sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildSqliteFast"
                            }
                            archiveArtifacts 'core/_buildfiles/packages/'
                        }
                        post {
                            always {
                                junit 'core/_buildfiles/build/logs/junit.xml'
                                step([$class: 'Mailer', notifyEveryUnstableBuild: true, recipients: emailextrecipients([[$class: 'CulpritsRecipientProvider'], [$class: 'RequesterRecipientProvider']])])
                            }
                        }
                    }

                    stage ('slave sourceguardian71') {
                        agent {
                            label 'sourceguardian71'
                        }
                        steps {
                            checkout([
                                $class: 'GitSCM', branches: scm.branches, extensions: [[$class: 'RelativeTargetDirectory', relativeTargetDir: 'core']], userRemoteConfigs: scm.userRemoteConfigs
                            ])

                            withAnt(installation: 'Ant') {
                                sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildSqliteFast"
                            }
                            archiveArtifacts 'core/_buildfiles/packages/'
                        }
                        post {
                            always {
                                junit 'core/_buildfiles/build/logs/junit.xml'
                                step([$class: 'Mailer', notifyEveryUnstableBuild: true, recipients: emailextrecipients([[$class: 'CulpritsRecipientProvider'], [$class: 'RequesterRecipientProvider']])])
                            }
                        }
                    }
                }
                
            }

        }
        
    }