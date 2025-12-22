# Makefile for JWT CTF Challenge
# Makes deployment easier with simple commands

.PHONY: help build up down restart logs shell clean rebuild test

# Default target
help:
	@echo "JWT CTF Challenge - Docker Commands"
	@echo "===================================="
	@echo "make build    - Build the Docker image"
	@echo "make up       - Start the CTF challenge"
	@echo "make down     - Stop the CTF challenge"
	@echo "make restart  - Restart the container"
	@echo "make logs     - View container logs"
	@echo "make shell    - Access container shell"
	@echo "make clean    - Remove containers and images"
	@echo "make rebuild  - Rebuild from scratch"
	@echo "make test     - Test if the CTF is accessible"
	@echo "make status   - Show container status"

# Build the Docker image
build:
	@echo "Building JWT CTF Docker image..."
	docker-compose build

# Start the CTF
up:
	@echo "Starting JWT CTF Challenge..."
	docker-compose up -d
	@echo "✅ CTF is running at http://localhost:8080"

# Stop the CTF
down:
	@echo "Stopping JWT CTF Challenge..."
	docker-compose down

# Restart the container
restart:
	@echo "Restarting JWT CTF Challenge..."
	docker-compose restart

# View logs
logs:
	docker-compose logs -f

# Access container shell
shell:
	docker exec -it jwt_ctf_challenge bash

# Clean up everything
clean:
	@echo "Cleaning up Docker resources..."
	docker-compose down -v
	docker rmi jwt-ctf:latest 2>/dev/null || true
	@echo "✅ Cleanup complete"

# Rebuild from scratch
rebuild: clean build up
	@echo "✅ Rebuild complete"

# Test if CTF is accessible
test:
	@echo "Testing CTF accessibility..."
	@curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" http://localhost:8080 || echo "❌ CTF is not accessible"

# Show container status
status:
	@echo "Container Status:"
	@docker-compose ps
	@echo "\nResource Usage:"
	@docker stats jwt_ctf_challenge --no-stream 2>/dev/null || echo "Container not running"

# Quick deploy (build and start)
deploy: build up
	@echo "✅ JWT CTF deployed successfully!"
	@echo "Access at: http://localhost:8080"