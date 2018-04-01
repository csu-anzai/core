#!groovy

@Library('art-shared@master') _
import static de.artemeon.Utilities.*

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

                    stage ('slave mssql') {
                        agent {
                            node {
                                label 'mssql'
                                customWorkspace "C:/j/workspace/KajonaCore_${BRANCH_NAME}"
                            }
                        }
                        steps {
                            checkout([
                                $class: 'GitSCM', branches: scm.branches, extensions: [[$class: 'RelativeTargetDirectory', relativeTargetDir: 'core']], userRemoteConfigs: scm.userRemoteConfigs
                            ])

                            withAnt(installation: 'Ant') {
                               // bat "ant -buildfile core/_buildfiles/build.xml buildSqliteFast"
                            }
                            //archiveArtifacts 'core/_buildfiles/packages/'
                        }
                        post {
                            always {
                                junit 'core/_buildfiles/build/logs/junit.xml'
                                step([$class: 'Mailer', notifyEveryUnstableBuild: true, recipients: emailextrecipients([[$class: 'CulpritsRecipientProvider'], [$class: 'RequesterRecipientProvider']])])
                            }
                        }
                    }
                    /*
                    stage ('slave php7') {
                        agent {
                            label 'php7'
                        }
                        steps {
                            checkout([
                                $class: 'GitSCM', branches: scm.branches, extensions: [[$class: 'RelativeTargetDirectory', relativeTargetDir: 'core']], userRemoteConfigs: scm.userRemoteConfigs
                            ])

                            withAnt(installation: 'Ant') {
                                sh "ant -buildfile core/_buildfiles/build.xml buildSqliteFast"
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
                                sh "ant -buildfile core/_buildfiles/build.xml buildSqliteFast"
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
                    */
                    
                }
                
            }

        }

        post {
            always {
                script {
                    mattermost('SUCCESS', '${env.JOB_NAME}', '${env.BUILD_NUMBER}', '${env.BUILD_URL}')√
                }
            }

        }
    }
