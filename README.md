# PM Search UI v2

## Docker usage

Build and test images locally using docker-compose:  
(from the project root directory)  
`docker-compose -f docker-compose.yml -f docker-compose.local.yml up --build -d`

Stop the containers:
`docker-compose -f docker-compose.yml -f docker-compose.local.yml down`


### Pushing to Azure
- `az acr login --name sgscoaisearchfrontend`
- `docker-compose push`
- `docker context use pm-search-ui-context`
- `docker-compose -f docker-compose.yml -f docker-compose.staging.yml up`

## Notes
### Building the superbase
- `cd dockerfiles`
- `docker build . -f superbase.dockerfile -t superbase:latest`
- `docker tag superbase:latest sgscoaisearchfrontend.azurecr.io/superbase:latest`
- `docker push sgscoaisearchfrontend.azurecr.io/superbase:latest`


### Docker / Azure auth
`docker login azure`
