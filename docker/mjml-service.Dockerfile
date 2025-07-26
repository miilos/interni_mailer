FROM node:18-alpine

WORKDIR /app

COPY mjml-service/package*.json ./
RUN npm install

COPY mjml-service/ .

EXPOSE 3000
CMD ["npm", "start"]
