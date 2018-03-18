pipeline {  
    agent any

    stages {

        stage ('Git Checkout') {
            steps {
                dir('core') {
                    checkout scm
                }
            }
        }

        stage('loadtest2') {
            steps {
                load "core/_buildfiles/buildsteps/buildStep2.groovy"
            }
        }

        stage('loadTest') {
            steps {
                load "core/_buildfiles/buildsteps/prepareAndBuild.groovy"
            }
        }

        stage('Prepare') {
            parallel {

                stage ('cleanFilesystem') {
                    steps {
                        withAnt(installation: 'Ant') {
                            script {
                                try {
                                    sh "ant -buildfile core/_buildfiles/build_jenkins.xml cleanFilesystem"
                                } catch (err) {

                                }
                            }
                        }
                    }
                }
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

/*
        stage ('phpunit') {
            parallel {
                stage ('php7') {
                    agent {
                        label 'php7'
                    }
                    steps {
                        sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildProject "
                        sh "ant -buildfile core/_buildfiles/build_jenkins.xml installProjectSqlite "
                        sh "ant -buildfile core/_buildfiles/build_jenkins.xml phpunit "
                    }
                    post {
                        always {
                            junit 'core/_buildfiles/build/logs/junit.xml'
                        }
                    }
                }

                stage ('php71') {
                    agent {
                        label 'sourceguardian71'
                    }
                    steps {
                        sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildProject "
                        sh "ant -buildfile core/_buildfiles/build_jenkins.xml installProjectSqlite "
                        sh "ant -buildfile core/_buildfiles/build_jenkins.xml phpunit "
                    }
                    post {
                        always {
                            junit 'core/_buildfiles/build/logs/junit.xml'
                        }
                    }
                }
            }
        }
*/

        //stage ('Publish xUnit') {
        //    steps {
        //        junit 'core/_buildfiles/build/logs/junit.xml'
                //step([$class: 'XUnitBuilder', testTimeMargin: '3000', thresholdMode: 1, thresholds: [[$class: 'FailedThreshold', failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0'], [$class: 'SkippedThreshold', failureNewThreshold: '1000', failureThreshold: '1000', unstableNewThreshold: '1000', unstableThreshold: '1000']], tools: [[$class: 'JUnitType', deleteOutputFiles: true, failIfNotNew: true, pattern: 'core/_buildfiles/build/logs/junit.xml', skipNoTestFiles: false, stopProcessingIfError: true]]])
        //    }
        //}
        

        stage ('Build Archive') {
            steps {
                // Ant build step
                withAnt(installation: 'Ant') {
                    sh "ant -buildfile core/_buildfiles/build_jenkins.xml buildFullZip "
                }

    		}
    	}

        

    	stage ('Archive') {
    	    steps {
    	        archiveArtifacts 'core/_buildfiles/packages/'
    	    }
    	}

    }
    post {
        always {
            step([$class: 'Mailer', notifyEveryUnstableBuild: true, recipients: emailextrecipients([[$class: 'CulpritsRecipientProvider'], [$class: 'RequesterRecipientProvider']])])
        }
    }
}

