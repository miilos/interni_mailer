FROM node:18-alpine

WORKDIR /app

COPY openai-service/package*.json ./
RUN npm install

COPY openai-service/ .

EXPOSE 4000
CMD ["npm", "start"]
